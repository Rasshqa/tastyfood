<?php

namespace App\Http\Controllers;

use App\Events\NewOrderReceived;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Setting;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('Order store attempt', [
            'delivery_method' => $request->delivery_method,
            'has_latitude' => $request->has('latitude'),
            'latitude_value' => $request->latitude,
            'has_longitude' => $request->has('longitude'),
            'longitude_value' => $request->longitude,
        ]);

        $validated = $request->validate([
            'customer_name' => 'required|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|max:20',
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_method,delivery|nullable|max:500',
            'latitude' => 'required_if:delivery_method,delivery|nullable|numeric',
            'longitude' => 'required_if:delivery_method,delivery|nullable|numeric',
            'notes' => 'nullable|max:500',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong');
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItems = [];

            foreach ($cart as $menuId => $item) {
                $menu = Menu::find($menuId);
                if (! $menu || ! $menu->available) {
                    DB::rollBack();

                    return back()->with('error', 'Menu tidak tersedia: '.($menu->name ?? 'Unknown'));
                }

                $itemSubtotal = $menu->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'menu_price' => $menu->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];

                $menu->increment('order_count', $item['quantity']);
            }

            // Calculate real delivery fee based on distance
            if ($validated['delivery_method'] === 'delivery') {
                $restaurantLat = (float) Setting::get('contact_latitude', config('restaurant.latitude'));
                $restaurantLng = (float) Setting::get('contact_longitude', config('restaurant.longitude'));

                // Validate within radius
                if (! DeliveryService::isWithinDeliveryRadius($validated['latitude'], $validated['longitude'])) {
                    DB::rollBack();

                    return back()->with('error', 'Lokasi pengiriman di luar jangkauan ('.config('restaurant.delivery_radius_km').' km)');
                }

                $distance = DeliveryService::calculateDistance(
                    $restaurantLat,
                    $restaurantLng,
                    $validated['latitude'],
                    $validated['longitude']
                );

                $deliveryFee = DeliveryService::calculateDeliveryFee($distance);

                // Free delivery if meets minimum
                $freeDeliveryMin = config('restaurant.free_delivery_min_order');
                if ($freeDeliveryMin > 0 && $subtotal >= $freeDeliveryMin) {
                    $deliveryFee = 0;
                }
            } else {
                $deliveryFee = 0;
            }

            $total = $subtotal + $deliveryFee;

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'delivery_method' => $validated['delivery_method'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            session()->forget('cart');

            DB::commit();

            broadcast(new NewOrderReceived($order));

            return redirect()->route('orders.success', $order->id)->with('success', 'Pesanan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function success($id)
    {
        $order = Order::with('items')->findOrFail($id);

        return view('orders.success', compact('order'));
    }

    public function history()
    {
        $orders = auth()->user()->orders()->with('payment')->latest()->paginate(10);

        return view('orders.history', compact('orders'));
    }

    public function completeOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'delivered') {
            return back()->with('error', 'Hanya pesanan yang sudah dikirim yang dapat dikonfirmasi.');
        }

        $order->update(['status' => 'completed']);
        return back()->with('success', 'Terima kasih! Pesanan telah dikonfirmasi.');
    }


    public function show($id)
    {
        $order = auth()->user()->orders()->with('items', 'payment.paymentMethod')->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    // AJAX endpoint for delivery fee calculation
    public function calculateDeliveryFee(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $restaurantLat = (float) Setting::get('contact_latitude', config('restaurant.latitude'));
        $restaurantLng = (float) Setting::get('contact_longitude', config('restaurant.longitude'));

        // Check if within delivery radius
        if (! DeliveryService::isWithinDeliveryRadius($validated['latitude'], $validated['longitude'])) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi di luar jangkauan delivery ('.config('restaurant.delivery_radius_km').' km)',
            ], 400);
        }

        $distance = DeliveryService::calculateDistance(
            $restaurantLat,
            $restaurantLng,
            $validated['latitude'],
            $validated['longitude']
        );

        $deliveryFee = DeliveryService::calculateDeliveryFee($distance);

        // Free delivery check
        $freeDeliveryMin = config('restaurant.free_delivery_min_order');
        $isFreeDelivery = $freeDeliveryMin > 0 && $validated['subtotal'] >= $freeDeliveryMin;

        if ($isFreeDelivery) {
            $deliveryFee = 0;
        }

        return response()->json([
            'success' => true,
            'distance' => $distance,
            'delivery_fee' => $deliveryFee,
            'is_free_delivery' => $isFreeDelivery,
            'formatted_fee' => 'Rp '.number_format($deliveryFee, 0, ',', '.'),
        ]);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DeliveryService
{
    /**
     * Calculate distance between two coordinates using Haversine formula.
     * Returns distance in kilometers.
     */
    public static function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371; // Earth radius in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $R * $c;

        return round($distance, 2);
    }

    /**
     * Calculate delivery fee based on distance and restaurant config.
     */
    public static function calculateDeliveryFee(float $distance): float
    {
        $feePerKm = (float) config('restaurant.delivery_fee_per_km', 2000);
        $minFee = (float) config('restaurant.min_delivery_fee', 10000);
        $freeDeliveryMinOrder = (float) config('restaurant.free_delivery_min_order', 100000);

        $calculatedFee = $distance * $feePerKm;

        // Apply minimum delivery fee
        $fee = max($calculatedFee, $minFee);

        // Free delivery if order total meets minimum
        // This check should be done in the controller when calculating total
        return round($fee);
    }

    /**
     * Validate if delivery location is within restaurant delivery radius.
     */
    public static function isWithinDeliveryRadius(float $lat, float $lng): bool
    {
        $restaurantLat = (float) config('restaurant.latitude');
        $restaurantLng = (float) config('restaurant.longitude');
        $radius = (float) config('restaurant.delivery_radius_km', 10);

        $distance = self::calculateDistance($restaurantLat, $restaurantLng, $lat, $lng);

        return $distance <= $radius;
    }

    /**
     * Geocode address to coordinates using Nominatim (OpenStreetMap).
     */
    public static function geocodeAddress(string $address): ?array
    {
        $url = 'https://nominatim.openstreetmap.org/search?'.http_build_query([
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
        ]);

        $response = Http::timeout(10)->get($url);

        if ($response->successful() && $response->json()) {
            $data = $response->json()[0];

            return [
                'lat' => (float) $data['lat'],
                'lng' => (float) $data['lon'],
                'display_name' => $data['display_name'],
            ];
        }

        return null;
    }

    /**
     * Reverse geocode coordinates to address using Nominatim.
     */
    public static function reverseGeocode(float $lat, float $lng): ?array
    {
        $url = 'https://nominatim.openstreetmap.org/reverse?'.http_build_query([
            'lat' => $lat,
            'lon' => $lng,
            'format' => 'json',
            'addressdetails' => 1,
        ]);

        $response = Http::timeout(10)->get($url);

        if ($response->successful() && $response->json()) {
            return [
                'address' => $response->json()['display_name'],
                'address_details' => $response->json()['address'] ?? [],
            ];
        }

        return null;
    }
}

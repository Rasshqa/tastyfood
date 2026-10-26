import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';
import '../../core/widgets/widgets.dart';
import '../../app/theme/app_spacing.dart';
import '../../core/utils/date_formatter.dart';
import 'orders_bloc.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({super.key});

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> {
  String? selectedStatus;
  String? selectedPaymentStatus;
  final searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    context.read<OrdersBloc>().add(OrdersLoadRequested());
  }

  @override
  void dispose() {
    searchController.dispose();
    super.dispose();
  }

  void _loadOrders() {
    context.read<OrdersBloc>().add(OrdersLoadRequested(
          status: selectedStatus,
          paymentStatus: selectedPaymentStatus,
          search: searchController.text,
        ));
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Pesanan',
      showBottomNav: true,
      currentIndex: 1,
      onNavTap: (index) {
        switch (index) {
          case 0:
            context.go('/dashboard');
            break;
          case 1:
            break;
          case 2:
            context.go('/chat');
            break;
          case 3:
            context.go('/more');
            break;
        }
      },
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh),
          onPressed: _loadOrders,
        ),
      ],
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(AppSpacing.md),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: searchController,
                    decoration: InputDecoration(
                      hintText: 'Cari pesanan...',
                      prefixIcon: const Icon(Icons.search),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(AppSpacing.sm),
                      ),
                      contentPadding: const EdgeInsets.symmetric(
                        horizontal: AppSpacing.md,
                        vertical: AppSpacing.sm,
                      ),
                    ),
                    onSubmitted: (_) => _loadOrders(),
                  ),
                ),
                const SizedBox(width: AppSpacing.sm),
                IconButton.filledTonal(
                  icon: const Icon(Icons.filter_list),
                  onPressed: () => _showFilterDialog(context),
                ),
              ],
            ),
          ),
          Expanded(child: BlocBuilder<OrdersBloc, OrdersState>(
            builder: (context, state) {
              if (state is OrdersLoading) {
                return const Center(child: CircularProgressIndicator());
              }
              if (state is OrdersError) {
                return Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(state.message),
                      TextButton(onPressed: _loadOrders, child: const Text('Coba Lagi')),
                    ],
                  ),
                );
              }
              if (state is OrdersLoaded) {
                return state.orders.isEmpty
                    ? const Center(child: Text('Belum ada pesanan'))
                    : ListView.builder(
                        padding: const EdgeInsets.all(AppSpacing.sm),
                        itemCount: state.orders.length,
                        itemBuilder: (context, index) {
                          final order = state.orders[index];
                          return OrderCard(
                            orderNumber: order.orderNumber,
                            customerName: order.customerName,
                            phoneNumber: order.customerPhone,
                            orderTime: DateFormatter.formatDateTime(order.createdAt),
                            total: RupiahText.format(order.total.toDouble()),
                            status: order.status,
                            paymentStatus: order.payment?.status ?? 'unpaid',
                            onTap: () => context.push('/orders/${order.id}'),
                          );
                        },
                      );
              }
              return const SizedBox.shrink();
            },
          )),
        ],
      ),
    );
  }

  void _showFilterDialog(BuildContext context) {
    showModalBottomSheet(
      context: context,
      builder: (context) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text('Filter', style: Theme.of(context).textTheme.titleLarge),
              const SizedBox(height: AppSpacing.lg),
              DropdownButtonFormField<String>(
                initialValue: selectedStatus,
                decoration: const InputDecoration(labelText: 'Status Pesanan'),
                items: const [
                  DropdownMenuItem(value: null, child: Text('Semua')),
                  DropdownMenuItem(value: 'pending', child: Text('Menunggu Konfirmasi')),
                  DropdownMenuItem(value: 'confirmed', child: Text('Dikonfirmasi')),
                  DropdownMenuItem(value: 'preparing', child: Text('Diproses')),
                  DropdownMenuItem(value: 'ready', child: Text('Siap')),
                  DropdownMenuItem(value: 'in_transit', child: Text('Dalam Perjalanan')),
                  DropdownMenuItem(value: 'delivered', child: Text('Selesai')),
                  DropdownMenuItem(value: 'cancelled', child: Text('Dibatalkan')),
                ],
                onChanged: (v) => setState(() => selectedStatus = v),
              ),
              const SizedBox(height: AppSpacing.md),
              DropdownButtonFormField<String>(
                initialValue: selectedPaymentStatus,
                decoration: const InputDecoration(labelText: 'Status Pembayaran'),
                items: const [
                  DropdownMenuItem(value: null, child: Text('Semua')),
                  DropdownMenuItem(value: 'unpaid', child: Text('Belum Dibayar')),
                  DropdownMenuItem(value: 'waiting_verification', child: Text('Menunggu Verifikasi')),
                  DropdownMenuItem(value: 'paid', child: Text('Lunas')),
                  DropdownMenuItem(value: 'rejected', child: Text('Ditolak')),
                ],
                onChanged: (v) => setState(() => selectedPaymentStatus = v),
              ),
              const SizedBox(height: AppSpacing.lg),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () {
                        setState(() {
                          selectedStatus = null;
                          selectedPaymentStatus = null;
                        });
                        Navigator.pop(context);
                        _loadOrders();
                      },
                      child: const Text('Reset'),
                    ),
                  ),
                  const SizedBox(width: AppSpacing.md),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        Navigator.pop(context);
                        _loadOrders();
                      },
                      child: const Text('Terapkan'),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}

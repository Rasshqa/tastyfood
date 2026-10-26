import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';
import '../../app/theme/app_typography.dart';
import '../../core/models/order.dart';
import '../../core/utils/date_formatter.dart';
import '../../core/widgets/widgets.dart';
import '../orders/orders_repository.dart';

class PaymentQueueScreen extends StatefulWidget {
  const PaymentQueueScreen({super.key});

  @override
  State<PaymentQueueScreen> createState() => _PaymentQueueScreenState();
}

class _PaymentQueueScreenState extends State<PaymentQueueScreen> {
  List<Order> _pendingOrders = [];
  bool _isLoading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _loadQueue();
  }

  Future<void> _loadQueue() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final repository = context.read<OrdersRepository>();
      final orders = await repository.getOrders(paymentStatus: 'waiting_verification');
      if (mounted) {
        setState(() {
          _pendingOrders = orders;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _errorMessage = e.toString().replaceAll('Exception: ', '');
          _isLoading = false;
        });
      }
    }
  }

  Future<void> _approvePayment(Order order) async {
    final payment = order.payment;
    if (payment == null) return;

    final confirmed = await ConfirmActionDialog.show(
      context,
      title: 'Setujui Pembayaran',
      message: 'Setujui pembayaran untuk pesanan ${order.orderNumber} sejumlah ${RupiahText.format(order.total.toDouble())}?',
      confirmLabel: 'Setujui',
      cancelLabel: 'Batal',
    );

    if (confirmed == true && mounted) {
      try {
        await context.read<OrdersRepository>().approvePayment(payment.id);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Pembayaran #${order.orderNumber} berhasil disetujui'),
              backgroundColor: AppColors.success,
            ),
          );
          _loadQueue();
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(e.toString()), backgroundColor: AppColors.error),
          );
        }
      }
    }
  }

  Future<void> _rejectPayment(Order order) async {
    final payment = order.payment;
    if (payment == null) return;

    final reason = await ConfirmActionDialog.prompt(
      context,
      title: 'Tolak Pembayaran',
      message: 'Pilih atau masukkan alasan penolakan bukti pembayaran untuk pesanan ${order.orderNumber}:',
      confirmLabel: 'Tolak',
      cancelLabel: 'Batal',
      isDestructive: true,
      textFieldLabel: 'Alasan Penolakan',
      textFieldHint: 'Tuliskan alasan penolakan...',
      minLength: 10,
      templates: const [
        'Bukti transfer tidak jelas. Mohon upload ulang.',
        'Nama pengirim tidak sesuai dengan nama pemesan.',
        'Jumlah transfer tidak sesuai dengan total pesanan.',
        'Bukti transfer sudah pernah digunakan.',
        'Format bukti transfer tidak valid.',
      ],
    );

    if (reason != null && mounted) {
      try {
        await context.read<OrdersRepository>().rejectPayment(payment.id, reason);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Pembayaran #${order.orderNumber} telah ditolak'),
              backgroundColor: AppColors.error,
            ),
          );
          _loadQueue();
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(e.toString()), backgroundColor: AppColors.error),
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Verifikasi Pembayaran',
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh),
          onPressed: _loadQueue,
        ),
      ],
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_errorMessage != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 48, color: AppColors.error),
              const SizedBox(height: AppSpacing.md),
              Text(_errorMessage!, textAlign: TextAlign.center, style: AppTypography.body),
              const SizedBox(height: AppSpacing.md),
              ElevatedButton(onPressed: _loadQueue, child: const Text('Coba Lagi')),
            ],
          ),
        ),
      );
    }

    if (_pendingOrders.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.xl),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: AppColors.success.withValues(alpha: 0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.check_circle_outline, size: 56, color: AppColors.success),
              ),
              const SizedBox(height: AppSpacing.md),
              Text('Semua Pembayaran Beres!', style: AppTypography.h3),
              const SizedBox(height: 8),
              Text(
                'Tidak ada bukti pembayaran yang sedang menunggu verifikasi.',
                textAlign: TextAlign.center,
                style: AppTypography.bodySmall.copyWith(color: AppColors.textSecondary),
              ),
            ],
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadQueue,
      child: ListView.builder(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: _pendingOrders.length,
        itemBuilder: (context, index) {
          final order = _pendingOrders[index];
          final proofUrl = order.payment?.proofImage;

          return Container(
            margin: const EdgeInsets.only(bottom: AppSpacing.md),
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.border),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.04),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: InkWell(
              borderRadius: BorderRadius.circular(16),
              onTap: () => context.push('/orders/${order.id}'),
              child: Padding(
                padding: const EdgeInsets.all(AppSpacing.md),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          order.orderNumber,
                          style: AppTypography.bodyMedium.copyWith(
                            fontWeight: FontWeight.bold,
                            color: AppColors.primary,
                          ),
                        ),
                        Text(
                          DateFormatter.formatDateTime(order.createdAt),
                          style: AppTypography.caption.copyWith(color: AppColors.textSecondary),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Text(
                      order.customerName,
                      style: AppTypography.h3.copyWith(fontSize: 16),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Total: ${RupiahText.format(order.total.toDouble())}',
                      style: AppTypography.bodyMedium.copyWith(
                        fontWeight: FontWeight.bold,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: AppSpacing.md),

                    // Proof Image Thumbnail & Zoom Action
                    if (proofUrl != null && proofUrl.isNotEmpty) ...[
                      GestureDetector(
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => ImagePreview(
                                imageUrl: proofUrl,
                                title: 'Bukti Pembayaran #${order.orderNumber}',
                              ),
                            ),
                          );
                        },
                        child: Container(
                          height: 120,
                          width: double.infinity,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: AppColors.border),
                          ),
                          clipBehavior: Clip.antiAlias,
                          child: Stack(
                            fit: StackFit.expand,
                            children: [
                              Image.network(
                                proofUrl,
                                fit: BoxFit.cover,
                                errorBuilder: (_, _, _) => Container(
                                  color: AppColors.background,
                                  alignment: Alignment.center,
                                  child: const Text('Bukti transfer tidak dapat dimuat'),
                                ),
                              ),
                              Positioned(
                                right: 8,
                                bottom: 8,
                                child: Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: Colors.black.withValues(alpha: 0.7),
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  child: const Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Icon(Icons.zoom_in, color: Colors.white, size: 14),
                                      SizedBox(width: 4),
                                      Text(
                                        'Perbesar',
                                        style: TextStyle(color: Colors.white, fontSize: 11),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: AppSpacing.md),
                    ],

                    // Action buttons: Approve / Reject
                    Row(
                      children: [
                        Expanded(
                          child: ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppColors.success,
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(vertical: 12),
                            ),
                            onPressed: () => _approvePayment(order),
                            icon: const Icon(Icons.check, size: 18),
                            label: const Text('Setujui'),
                          ),
                        ),
                        const SizedBox(width: AppSpacing.sm),
                        Expanded(
                          child: OutlinedButton.icon(
                            style: OutlinedButton.styleFrom(
                              foregroundColor: AppColors.error,
                              side: const BorderSide(color: AppColors.error),
                              padding: const EdgeInsets.symmetric(vertical: 12),
                            ),
                            onPressed: () => _rejectPayment(order),
                            icon: const Icon(Icons.close, size: 18),
                            label: const Text('Tolak'),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

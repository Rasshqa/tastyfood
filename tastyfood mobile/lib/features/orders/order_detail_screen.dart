import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';
import '../../app/theme/app_typography.dart';
import '../../core/models/order.dart';
import '../../core/utils/date_formatter.dart';
import '../../core/widgets/widgets.dart';
import 'orders_bloc.dart';
import 'orders_repository.dart';

class OrderDetailScreen extends StatefulWidget {
  final int orderId;

  const OrderDetailScreen({super.key, required this.orderId});

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  Order? _order;
  bool _isLoading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _loadOrderDetail();
  }

  Future<void> _loadOrderDetail() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final repository = context.read<OrdersRepository>();
      final order = await repository.getOrderDetail(widget.orderId);
      if (mounted) {
        setState(() {
          _order = order;
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

  Future<void> _updateStatus(String newStatus, String label) async {
    final confirmed = await ConfirmActionDialog.show(
      context,
      title: 'Ubah Status Pesanan',
      message: 'Apakah Anda yakin ingin mengubah status pesanan menjadi "$label"?',
      confirmLabel: 'Ya, Ubah',
      cancelLabel: 'Batal',
    );

    if (confirmed == true && mounted) {
      context.read<OrdersBloc>().add(OrderStatusUpdateRequested(widget.orderId, newStatus));
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Status pesanan diubah ke $label')),
      );
      _loadOrderDetail();
    }
  }

  Future<void> _cancelOrder() async {
    final reason = await ConfirmActionDialog.prompt(
      context,
      title: 'Batalkan Pesanan',
      message: 'Masukkan alasan pembatalan pesanan untuk pelanggan.',
      confirmLabel: 'Batalkan Pesanan',
      cancelLabel: 'Tutup',
      isDestructive: true,
      textFieldLabel: 'Alasan Pembatalan',
      textFieldHint: 'Contoh: Stok bahan habis, restoran tutup sementara...',
      minLength: 5,
      templates: const [
        'Stok bahan baku habis',
        'Restoran sedang penuh',
        'Alamat di luar jangkauan pengantaran',
        'Pelanggan meminta pembatalan',
      ],
    );

    if (reason != null && mounted) {
      context.read<OrdersBloc>().add(OrderStatusUpdateRequested(widget.orderId, 'cancelled'));
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pesanan berhasil dibatalkan')),
      );
      _loadOrderDetail();
    }
  }

  Future<void> _approvePayment(int paymentId) async {
    final confirmed = await ConfirmActionDialog.show(
      context,
      title: 'Verifikasi Pembayaran',
      message: 'Apakah bukti transfer valid dan pembayaran sebesar ${RupiahText.format(_order?.total.toDouble() ?? 0)} telah diterima di rekening?',
      confirmLabel: 'Setujui',
      cancelLabel: 'Batal',
    );

    if (confirmed == true && mounted) {
      context.read<OrdersBloc>().add(PaymentApproveRequested(paymentId));
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pembayaran berhasil disetujui')),
      );
      _loadOrderDetail();
    }
  }

  Future<void> _rejectPayment(int paymentId) async {
    final reason = await ConfirmActionDialog.prompt(
      context,
      title: 'Tolak Pembayaran',
      message: 'Pilih atau tuliskan alasan penolakan bukti pembayaran.',
      confirmLabel: 'Tolak Pembayaran',
      cancelLabel: 'Batal',
      isDestructive: true,
      textFieldLabel: 'Alasan Penolakan',
      textFieldHint: 'Jelaskan kenapa bukti transfer ditolak...',
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
      context.read<OrdersBloc>().add(PaymentRejectRequested(paymentId, reason));
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pembayaran ditolak')),
      );
      _loadOrderDetail();
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Detail Pesanan',
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh),
          onPressed: _loadOrderDetail,
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
              Text(
                _errorMessage!,
                textAlign: TextAlign.center,
                style: AppTypography.body,
              ),
              const SizedBox(height: AppSpacing.lg),
              ElevatedButton(
                onPressed: _loadOrderDetail,
                child: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
      );
    }

    final order = _order;
    if (order == null) {
      return const Center(child: Text('Data pesanan tidak ditemukan'));
    }

    return RefreshIndicator(
      onRefresh: _loadOrderDetail,
      child: ListView(
        padding: const EdgeInsets.all(AppSpacing.md),
        children: [
          // Order Header Card
          _buildHeaderCard(order),
          const SizedBox(height: AppSpacing.md),

          // Payment Verification Notice / Action Card (if awaiting verification)
          if (order.payment?.status == 'waiting_verification') ...[
            _buildPaymentActionCard(order),
            const SizedBox(height: AppSpacing.md),
          ],

          // Customer Info Card
          _buildCustomerCard(order),
          const SizedBox(height: AppSpacing.md),

          // Order Items Card
          _buildItemsCard(order),
          const SizedBox(height: AppSpacing.md),

          // Cost Breakdown Card
          _buildCostBreakdownCard(order),
          const SizedBox(height: AppSpacing.md),

          // Payment Info Card
          _buildPaymentDetailsCard(order),
          const SizedBox(height: AppSpacing.lg),

          // Status Transition Actions
          _buildStatusActions(order),
          const SizedBox(height: AppSpacing.xl),
        ],
      ),
    );
  }

  Widget _buildHeaderCard(Order order) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    order.orderNumber,
                    style: AppTypography.h3.copyWith(color: AppColors.primary),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    DateFormatter.formatDateTime(order.createdAt),
                    style: AppTypography.caption.copyWith(color: AppColors.textSecondary),
                  ),
                ],
              ),
              StatusBadge(
                status: order.status,
                type: StatusType.order,
              ),
            ],
          ),
          const Divider(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Icon(
                    order.deliveryMethod == 'delivery'
                        ? Icons.delivery_dining
                        : Icons.takeout_dining,
                    color: AppColors.primaryDark,
                    size: 20,
                  ),
                  const SizedBox(width: 8),
                  Text(
                    order.deliveryMethod == 'delivery' ? 'Pengantaran (Delivery)' : 'Ambil Sendiri (Pickup)',
                    style: AppTypography.bodySmall.copyWith(fontWeight: FontWeight.w600),
                  ),
                ],
              ),
              StatusBadge(
                status: order.payment?.status ?? 'unpaid',
                type: StatusType.payment,
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentActionCard(Order order) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.warning.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.warning.withValues(alpha: 0.4)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.warning_amber_rounded, color: AppColors.warning),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  'Perlu Verifikasi Pembayaran Segera',
                  style: AppTypography.bodySmall.copyWith(
                    fontWeight: FontWeight.bold,
                    color: AppColors.warning,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            'Pelanggan telah mengunggah bukti pembayaran. Periksa bukti transfer di bawah lalu setujui atau tolak.',
            style: AppTypography.caption.copyWith(color: AppColors.textPrimary),
          ),
          const SizedBox(height: AppSpacing.md),
          Row(
            children: [
              Expanded(
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.success,
                    foregroundColor: Colors.white,
                  ),
                  onPressed: () => _approvePayment(order.payment!.id),
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
                  ),
                  onPressed: () => _rejectPayment(order.payment!.id),
                  icon: const Icon(Icons.close, size: 18),
                  label: const Text('Tolak'),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildCustomerCard(Order order) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Informasi Pelanggan', style: AppTypography.h3),
              IconButton(
                icon: const Icon(Icons.copy, size: 18, color: AppColors.textSecondary),
                tooltip: 'Salin nomor HP',
                onPressed: () {
                  Clipboard.setData(ClipboardData(text: order.customerPhone));
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Nomor HP berhasil disalin')),
                  );
                },
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          _buildInfoRow(Icons.person, 'Nama', order.customerName),
          const SizedBox(height: 8),
          _buildInfoRow(Icons.phone, 'Telepon', order.customerPhone),
          if (order.customerEmail != null && order.customerEmail!.isNotEmpty) ...[
            const SizedBox(height: 8),
            _buildInfoRow(Icons.email, 'Email', order.customerEmail!),
          ],
          if (order.deliveryAddress != null && order.deliveryAddress!.isNotEmpty) ...[
            const SizedBox(height: 8),
            _buildInfoRow(Icons.location_on, 'Alamat Pengiriman', order.deliveryAddress!),
          ],
          if (order.notes != null && order.notes!.isNotEmpty) ...[
            const SizedBox(height: 8),
            _buildInfoRow(Icons.note, 'Catatan', order.notes!, isHighlighted: true),
          ],
        ],
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value, {bool isHighlighted = false}) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 18, color: isHighlighted ? AppColors.accentOrange : AppColors.textSecondary),
        const SizedBox(width: 8),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: AppTypography.caption.copyWith(color: AppColors.textSecondary)),
              Text(
                value,
                style: AppTypography.bodySmall.copyWith(
                  fontWeight: isHighlighted ? FontWeight.w600 : FontWeight.w400,
                  color: isHighlighted ? AppColors.accentOrange : AppColors.textPrimary,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildItemsCard(Order order) {
    final items = order.items ?? [];
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Pesanan Menu', style: AppTypography.h3),
              Text(
                '${items.fold(0, (sum, i) => sum + i.quantity)} porsi',
                style: AppTypography.label.copyWith(color: AppColors.primary),
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          if (items.isEmpty)
            const Text('Tidak ada item menu tercatat')
          else
            ListView.separated(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: items.length,
              separatorBuilder: (_, _) => const Divider(height: 16),
              itemBuilder: (context, index) {
                final item = items[index];
                return Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      width: 32,
                      height: 32,
                      decoration: BoxDecoration(
                        color: AppColors.primary.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        '${item.quantity}x',
                        style: AppTypography.label.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                    const SizedBox(width: AppSpacing.sm),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            item.menuName,
                            style: AppTypography.bodyMedium,
                          ),
                          Text(
                            RupiahText.format(item.menuPrice.toDouble()),
                            style: AppTypography.caption.copyWith(color: AppColors.textSecondary),
                          ),
                        ],
                      ),
                    ),
                    Text(
                      RupiahText.format(item.subtotal.toDouble()),
                      style: AppTypography.bodyMedium.copyWith(fontWeight: FontWeight.bold),
                    ),
                  ],
                );
              },
            ),
        ],
      ),
    );
  }

  Widget _buildCostBreakdownCard(Order order) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Rincian Pembayaran', style: AppTypography.h3),
          const SizedBox(height: AppSpacing.md),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Subtotal Menu', style: AppTypography.bodySmall),
              Text(RupiahText.format(order.subtotal.toDouble()), style: AppTypography.bodySmall),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Ongkos Kirim', style: AppTypography.bodySmall),
              Text(
                order.deliveryFee > 0 ? RupiahText.format(order.deliveryFee.toDouble()) : 'Gratis',
                style: AppTypography.bodySmall,
              ),
            ],
          ),
          const Divider(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Total Pembayaran', style: AppTypography.bodyMedium.copyWith(fontWeight: FontWeight.bold)),
              Text(
                RupiahText.format(order.total.toDouble()),
                style: AppTypography.h3.copyWith(color: AppColors.primary),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentDetailsCard(Order order) {
    final payment = order.payment;
    final paymentMethod = payment?.paymentMethod;

    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Status & Metode Bayar', style: AppTypography.h3),
              StatusBadge(
                status: payment?.status ?? 'unpaid',
                type: StatusType.payment,
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          if (paymentMethod != null) ...[
            _buildInfoRow(Icons.account_balance, 'Metode', paymentMethod.name),
            if (paymentMethod.accountNumber != null) ...[
              const SizedBox(height: 8),
              _buildInfoRow(Icons.credit_card, 'No. Rekening', paymentMethod.accountNumber!),
            ],
            if (paymentMethod.accountName != null) ...[
              const SizedBox(height: 8),
              _buildInfoRow(Icons.badge, 'Atas Nama', paymentMethod.accountName!),
            ],
          ] else ...[
            _buildInfoRow(Icons.payment, 'Metode', 'Transfer Bank / Tunai'),
          ],
          if (payment?.rejectionReason != null) ...[
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(AppSpacing.sm),
              decoration: BoxDecoration(
                color: AppColors.error.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Icon(Icons.info_outline, size: 16, color: AppColors.error),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'Alasan penolakan: ${payment!.rejectionReason}',
                      style: AppTypography.caption.copyWith(color: AppColors.error),
                    ),
                  ),
                ],
              ),
            ),
          ],
          if (payment?.proofImage != null && payment!.proofImage!.isNotEmpty) ...[
            const SizedBox(height: AppSpacing.md),
            Text('Bukti Transfer:', style: AppTypography.label),
            const SizedBox(height: AppSpacing.xs),
            GestureDetector(
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => ImagePreview(
                      imageUrl: payment.proofImage!,
                      title: 'Bukti Pembayaran #${order.orderNumber}',
                    ),
                  ),
                );
              },
              child: Stack(
                alignment: Alignment.center,
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: Image.network(
                      payment.proofImage!,
                      height: 160,
                      width: double.infinity,
                      fit: BoxFit.cover,
                      errorBuilder: (_, _, _) => Container(
                        height: 100,
                        color: AppColors.background,
                        alignment: Alignment.center,
                        child: const Text('Bukti transfer tidak dapat dimuat'),
                      ),
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.black.withValues(alpha: 0.6),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.zoom_in, color: Colors.white, size: 16),
                        SizedBox(width: 4),
                        Text('Ketuk untuk Perbesar', style: TextStyle(color: Colors.white, fontSize: 12)),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildStatusActions(Order order) {
    final status = order.status;
    final List<Widget> actionButtons = [];

    switch (status) {
      case 'pending':
        actionButtons.add(
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primary,
              foregroundColor: Colors.white,
              minimumSize: const Size(double.infinity, 48),
            ),
            onPressed: () => _updateStatus('confirmed', 'Dikonfirmasi'),
            icon: const Icon(Icons.check_circle_outline),
            label: const Text('Konfirmasi Pesanan'),
          ),
        );
        actionButtons.add(const SizedBox(height: AppSpacing.sm));
        actionButtons.add(
          OutlinedButton.icon(
            style: OutlinedButton.styleFrom(
              foregroundColor: AppColors.error,
              side: const BorderSide(color: AppColors.error),
              minimumSize: const Size(double.infinity, 48),
            ),
            onPressed: _cancelOrder,
            icon: const Icon(Icons.cancel_outlined),
            label: const Text('Batalkan Pesanan'),
          ),
        );
        break;

      case 'confirmed':
        actionButtons.add(
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.accentOrange,
              foregroundColor: Colors.white,
              minimumSize: const Size(double.infinity, 48),
            ),
            onPressed: () => _updateStatus('preparing', 'Diproses (Dimasak)'),
            icon: const Icon(Icons.soup_kitchen_outlined),
            label: const Text('Mulai Proses Memasak'),
          ),
        );
        actionButtons.add(const SizedBox(height: AppSpacing.sm));
        actionButtons.add(
          OutlinedButton(
            style: OutlinedButton.styleFrom(
              foregroundColor: AppColors.error,
              side: const BorderSide(color: AppColors.error),
              minimumSize: const Size(double.infinity, 48),
            ),
            onPressed: _cancelOrder,
            child: const Text('Batalkan Pesanan'),
          ),
        );
        break;

      case 'preparing':
        actionButtons.add(
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.success,
              foregroundColor: Colors.white,
              minimumSize: const Size(double.infinity, 48),
            ),
            onPressed: () => _updateStatus('ready', 'Siap Disajikan / Diantar'),
            icon: const Icon(Icons.check),
            label: const Text('Tandai Siap (Ready)'),
          ),
        );
        break;

      case 'ready':
        if (order.deliveryMethod == 'delivery') {
          actionButtons.add(
            ElevatedButton.icon(
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primaryDark,
                foregroundColor: Colors.white,
                minimumSize: const Size(double.infinity, 48),
              ),
              onPressed: () => _updateStatus('in_transit', 'Dalam Perjalanan'),
              icon: const Icon(Icons.delivery_dining),
              label: const Text('Mulai Pengantaran (In Transit)'),
            ),
          );
        } else {
          actionButtons.add(
            ElevatedButton.icon(
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.success,
                foregroundColor: Colors.white,
                minimumSize: const Size(double.infinity, 48),
              ),
              onPressed: () => _updateStatus('delivered', 'Selesai (Diambil)'),
              icon: const Icon(Icons.done_all),
              label: const Text('Selesai (Pelanggan Ambil)'),
            ),
          );
        }
        break;

      case 'in_transit':
        actionButtons.add(
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.success,
              foregroundColor: Colors.white,
              minimumSize: const Size(double.infinity, 48),
            ),
            onPressed: () => _updateStatus('delivered', 'Terkirim'),
            icon: const Icon(Icons.done_all),
            label: const Text('Tandai Pesanan Terkirim'),
          ),
        );
        break;

      case 'delivered':
        actionButtons.add(
          ElevatedButton.icon(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primary,
              foregroundColor: Colors.white,
              minimumSize: const Size(double.infinity, 48),
            ),
            onPressed: () => _updateStatus('completed', 'Selesai Sempurna'),
            icon: const Icon(Icons.task_alt),
            label: const Text('Selesaikan Pesanan'),
          ),
        );
        break;

      default:
        break;
    }

    if (actionButtons.isEmpty) {
      return const SizedBox.shrink();
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        Text('Tindakan Status', style: AppTypography.label),
        const SizedBox(height: AppSpacing.sm),
        ...actionButtons,
      ],
    );
  }
}

import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_typography.dart';
import '../../app/theme/app_spacing.dart';
import 'status_badge.dart';
import 'loading_skeleton.dart';

/// Order card for order list.
class OrderCard extends StatelessWidget {
  final String orderNumber;
  final String customerName;
  final String phoneNumber;
  final String orderTime;
  final String total;
  final String status;
  final String paymentStatus;
  final VoidCallback? onTap;
  final bool showPaymentBadge;

  const OrderCard({
    super.key,
    required this.orderNumber,
    required this.customerName,
    required this.phoneNumber,
    required this.orderTime,
    required this.total,
    required this.status,
    required this.paymentStatus,
    this.onTap,
    this.showPaymentBadge = true,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: AppSpacing.sm),
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
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        orderNumber,
                        style: AppTypography.bodyMedium.copyWith(
                          color: AppColors.textPrimary,
                        ),
                      ),
                      Text(
                        orderTime,
                        style: AppTypography.caption.copyWith(
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    StatusBadge(
                      status: status,
                      type: StatusType.order,
                      compact: true,
                    ),
                    if (showPaymentBadge) ...[
                      const SizedBox(height: AppSpacing.xs),
                      StatusBadge(
                        status: paymentStatus,
                        type: StatusType.payment,
                        compact: true,
                      ),
                    ],
                  ],
                ),
              ],
            ),
            const SizedBox(height: AppSpacing.sm),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        customerName,
                        style: AppTypography.body,
                      ),
                      Text(
                        phoneNumber,
                        style: AppTypography.caption.copyWith(
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
                Text(
                  total,
                  style: AppTypography.h3.copyWith(
                    color: AppColors.primary,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

/// Payment queue card for payment verification list.
class PaymentQueueCard extends StatelessWidget {
  final String orderNumber;
  final String customerName;
  final String amount;
  final String paymentMethod;
  final String uploadedTime;
  final String paymentStatus;
  final String? proofUrl;
  final VoidCallback? onTap;

  const PaymentQueueCard({
    super.key,
    required this.orderNumber,
    required this.customerName,
    required this.amount,
    required this.paymentMethod,
    required this.uploadedTime,
    required this.paymentStatus,
    this.proofUrl,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(AppSpacing.md),
        margin: const EdgeInsets.only(bottom: AppSpacing.sm),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
        ),
        child: Row(
          children: [
            Container(
              width: 70,
              height: 70,
              decoration: BoxDecoration(
                color: AppColors.border,
                borderRadius: BorderRadius.circular(12),
              ),
              child: proofUrl != null
                  ? CachedNetworkImage(
                      imageUrl: proofUrl!,
                      fit: BoxFit.cover,
                      placeholder: (context, url) => const LoadingSkeleton(width: 50, height: 50, borderRadius: 12),
                      errorWidget: (context, url, error) => const Icon(
                        Icons.image_not_supported,
                        color: AppColors.textDisabled,
                      ),
                    )
                  : const Icon(
                      Icons.image_not_supported,
                      color: AppColors.textDisabled,
                    ),
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          orderNumber,
                          style: AppTypography.bodyMedium,
                        ),
                      ),
                      StatusBadge(
                        status: paymentStatus,
                        type: StatusType.payment,
                        compact: true,
                      ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    customerName,
                    style: AppTypography.body,
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Row(
                    children: [
                      Text(
                        paymentMethod,
                        style: AppTypography.label.copyWith(
                          color: AppColors.textSecondary,
                        ),
                      ),
                      const SizedBox(width: AppSpacing.md),
                      Text(
                        uploadedTime,
                        style: AppTypography.caption,
                      ),
                    ],
                  ),
                ],
              ),
            ),
            Text(
              amount,
              style: AppTypography.h3.copyWith(
                color: AppColors.primary,
                fontWeight: FontWeight.w700,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

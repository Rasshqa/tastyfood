import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';

/// Skeleton loader that mimics content shape during loading.
class LoadingSkeleton extends StatelessWidget {
  final double? width;
  final double height;
  final double borderRadius;

  const LoadingSkeleton({
    super.key,
    this.width,
    this.height = 16,
    this.borderRadius = 8,
  });

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: AppColors.border,
      highlightColor: AppColors.surface,
      child: Container(
        width: width,
        height: height,
        decoration: BoxDecoration(
          color: AppColors.border,
          borderRadius: BorderRadius.circular(borderRadius),
        ),
      ),
    );
  }
}

/// Skeleton for order card.
class OrderCardSkeleton extends StatelessWidget {
  const OrderCardSkeleton({super.key});

  @override
  Widget build(BuildContext context) {
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
              const LoadingSkeleton(width: 100, height: 16),
              const SizedBox(width: AppSpacing.sm),
              LoadingSkeleton(width: 80, height: 24, borderRadius: 12),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          const LoadingSkeleton(width: 150, height: 14),
          const SizedBox(height: AppSpacing.xs),
          const LoadingSkeleton(width: 120, height: 14),
          const SizedBox(height: AppSpacing.sm),
          const Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              LoadingSkeleton(width: 100, height: 18),
              LoadingSkeleton(width: 80, height: 20),
            ],
          ),
        ],
      ),
    );
  }
}

/// Skeleton for metric card.
class MetricCardSkeleton extends StatelessWidget {
  const MetricCardSkeleton({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
      ),
      child: const Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          LoadingSkeleton(width: 80, height: 14),
          SizedBox(height: AppSpacing.sm),
          LoadingSkeleton(width: 120, height: 28),
        ],
      ),
    );
  }
}

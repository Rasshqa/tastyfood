import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_typography.dart';
import '../../app/theme/app_spacing.dart';

/// Menu card for menu management list.
class MenuCard extends StatelessWidget {
  final String name;
  final String price;
  final String category;
  final String? imageUrl;
  final bool isInStock;
  final VoidCallback? onTap;
  final ValueChanged<bool>? onAvailabilityChanged;
  final VoidCallback? onEdit;
  final VoidCallback? onDelete;

  const MenuCard({
    super.key,
    required this.name,
    required this.price,
    required this.category,
    this.imageUrl,
    this.isInStock = true,
    this.onTap,
    this.onAvailabilityChanged,
    this.onEdit,
    this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: AppSpacing.sm),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.md),
          child: Row(
            children: [
              Container(
                width: 70,
                height: 70,
                decoration: BoxDecoration(
                  color: AppColors.border,
                  borderRadius: BorderRadius.circular(12),
                ),
                clipBehavior: Clip.antiAlias,
                child: imageUrl != null
                    ? CachedNetworkImage(
                        imageUrl: imageUrl!,
                        fit: BoxFit.cover,
                        placeholder: (context, url) => const Center(
                          child: CircularProgressIndicator(strokeWidth: 2),
                        ),
                        errorWidget: (context, url, error) => const Icon(
                          Icons.restaurant,
                          color: AppColors.textDisabled,
                        ),
                      )
                    : const Icon(
                        Icons.restaurant,
                        color: AppColors.textDisabled,
                        size: 32,
                      ),
              ),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      name,
                      style: AppTypography.bodyMedium.copyWith(
                        color: isInStock ? AppColors.textPrimary : AppColors.textDisabled,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      category,
                      style: AppTypography.label.copyWith(
                        color: AppColors.textSecondary,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      price,
                      style: AppTypography.bodyMedium.copyWith(
                        color: AppColors.primary,
                      ),
                    ),
                  ],
                ),
              ),
              Switch(
                value: isInStock,
                onChanged: onAvailabilityChanged,
                activeThumbColor: AppColors.success,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

import 'package:flutter/material.dart';
import '../../app/theme/status_colors.dart';
import '../../app/theme/app_typography.dart';
import '../../app/theme/app_spacing.dart';

/// Status badge with background and text color from StatusColors.
///
/// Always displays a text label alongside color for accessibility.
class StatusBadge extends StatelessWidget {
  final String status;
  final StatusType type;
  final bool compact;

  const StatusBadge({
    super.key,
    required this.status,
    required this.type,
    this.compact = false,
  });

  @override
  Widget build(BuildContext context) {
    final pair = type == StatusType.order
        ? StatusColors.getOrderStatus(status)
        : StatusColors.getPaymentStatus(status);

    return Container(
      padding: compact
          ? const EdgeInsets.symmetric(horizontal: 8, vertical: 4)
          : const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: pair.background,
        borderRadius: BorderRadius.circular(AppRadius.small),
      ),
      child: Text(
        pair.label,
        style: (compact ? AppTypography.caption : AppTypography.label).copyWith(
          color: pair.text,
        ),
      ),
    );
  }
}

enum StatusType { order, payment }

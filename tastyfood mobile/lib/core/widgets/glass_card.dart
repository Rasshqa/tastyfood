import 'dart:ui';
import 'package:flutter/material.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';

/// A glassmorphism card with blur, translucent fill, and soft border.
///
/// Use for:
/// - Dashboard summary cards
/// - Quick actions
/// - Filter panels / bottom sheets
/// - Header elements
///
/// Use solid white cards for:
/// - Long forms
/// - Dense lists
/// - Order details with lots of text
/// - Payment proof that needs maximum contrast
class GlassCard extends StatelessWidget {
  final Widget child;
  final double? width;
  final double? height;
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final double borderRadius;
  final double blurSigma;
  final double fillOpacity;
  final double borderOpacity;
  final VoidCallback? onTap;

  const GlassCard({
    super.key,
    required this.child,
    this.width,
    this.height,
    this.padding,
    this.margin,
    this.borderRadius = AppRadius.large,
    this.blurSigma = 14,
    this.fillOpacity = 0.78,
    this.borderOpacity = 0.72,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final card = ClipRRect(
      borderRadius: BorderRadius.circular(borderRadius),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: blurSigma, sigmaY: blurSigma),
        child: Container(
          width: width,
          height: height,
          decoration: BoxDecoration(
            color: AppColors.glassFill(AppColors.glassTint, opacity: fillOpacity),
            borderRadius: BorderRadius.circular(borderRadius),
            border: Border.all(
              color: AppColors.glassBorder(AppColors.glassTint, opacity: borderOpacity),
              width: 1,
            ),
            boxShadow: const [
              BoxShadow(
                color: Color(0x0D111827),
                blurRadius: 22,
                offset: Offset(0, 8),
              ),
            ],
          ),
          padding: padding ?? const EdgeInsets.all(AppSpacing.md),
          child: child,
        ),
      ),
    );

    if (onTap != null) {
      return GestureDetector(
        onTap: onTap,
        child: margin != null ? Padding(padding: margin!, child: card) : card,
      );
    }

    return margin != null ? Padding(padding: margin!, child: card) : card;
  }
}

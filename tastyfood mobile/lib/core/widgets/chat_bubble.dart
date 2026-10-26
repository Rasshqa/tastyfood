import 'package:flutter/material.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_typography.dart';
import '../../app/theme/app_spacing.dart';

/// Chat bubble for conversation screen.
class ChatBubble extends StatelessWidget {
  final String message;
  final DateTime timestamp;
  final bool isAdmin;
  final bool isSystem;
  final bool isSending;
  final bool isFailed;
  final VoidCallback? onRetry;

  const ChatBubble({
    super.key,
    required this.message,
    required this.timestamp,
    this.isAdmin = false,
    this.isSystem = false,
    this.isSending = false,
    this.isFailed = false,
    this.onRetry,
  });

  @override
  Widget build(BuildContext context) {
    if (isSystem) {
      return _buildSystemBubble();
    }

    return Align(
      alignment: isAdmin ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: EdgeInsets.only(
          left: isAdmin ? 64 : AppSpacing.md,
          right: isAdmin ? AppSpacing.md : 64,
          bottom: AppSpacing.sm,
        ),
        padding: const EdgeInsets.all(AppSpacing.md),
        decoration: BoxDecoration(
          color: isAdmin ? AppColors.primary : AppColors.border,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(16),
            topRight: const Radius.circular(16),
            bottomLeft: Radius.circular(isAdmin ? 16 : 4),
            bottomRight: Radius.circular(isAdmin ? 4 : 16),
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            Text(
              message,
              style: AppTypography.body.copyWith(
                color: isAdmin ? Colors.white : AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 4),
            Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  _formatTime(timestamp),
                  style: AppTypography.caption.copyWith(
                    color: isAdmin
                        ? Colors.white.withValues(alpha: 0.7)
                        : AppColors.textSecondary,
                  ),
                ),
                if (isAdmin) ...[
                  const SizedBox(width: 4),
                  if (isSending)
                    SizedBox(
                      width: 12,
                      height: 12,
                      child: CircularProgressIndicator(
                        strokeWidth: 1.5,
                        valueColor: AlwaysStoppedAnimation(
                          Colors.white.withValues(alpha: 0.7),
                        ),
                      ),
                    )
                  else if (isFailed)
                    GestureDetector(
                      onTap: onRetry,
                      child: Icon(
                        Icons.error_outline,
                        size: 14,
                        color: AppColors.error,
                      ),
                    )
                  else
                    Icon(
                      Icons.done_all,
                      size: 14,
                      color: Colors.white.withValues(alpha: 0.7),
                    ),
                ],
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSystemBubble() {
    return Center(
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: AppSpacing.sm),
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.md,
          vertical: AppSpacing.sm,
        ),
        decoration: BoxDecoration(
          color: AppColors.accentLight,
          borderRadius: BorderRadius.circular(20),
        ),
        child: Text(
          message,
          style: AppTypography.label.copyWith(
            color: AppColors.primary,
          ),
        ),
      ),
    );
  }

  String _formatTime(DateTime time) {
    return '${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')}';
  }
}

import 'package:flutter/material.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_typography.dart';
import '../../app/theme/app_spacing.dart';

/// Avatar widget with initials fallback.
class Avatar extends StatelessWidget {
  final String? imageUrl;
  final String name;
  final double radius;

  const Avatar({
    super.key,
    this.imageUrl,
    required this.name,
    this.radius = 24,
  });

  String get _initials {
    final parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    }
    return name.isNotEmpty ? name[0].toUpperCase() : '?';
  }

  @override
  Widget build(BuildContext context) {
    if (imageUrl != null && imageUrl!.isNotEmpty) {
      return CircleAvatar(
        radius: radius,
        backgroundImage: NetworkImage(imageUrl!),
        onBackgroundImageError: (_, _) {},
        child: Container(),
      );
    }

    return CircleAvatar(
      radius: radius,
      backgroundColor: AppColors.accentLight,
      child: Text(
        _initials,
        style: AppTypography.label.copyWith(
          color: AppColors.primary,
          fontSize: radius * 0.7,
        ),
      ),
    );
  }
}

/// Conversation tile for chat inbox.
class ConversationTile extends StatelessWidget {
  final String customerName;
  final String? avatarUrl;
  final String lastMessage;
  final String time;
  final int unreadCount;
  final String? orderNumber;
  final bool isOpen;
  final VoidCallback? onTap;

  const ConversationTile({
    super.key,
    required this.customerName,
    this.avatarUrl,
    required this.lastMessage,
    required this.time,
    this.unreadCount = 0,
    this.orderNumber,
    this.isOpen = true,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return ListTile(
      onTap: onTap,
      contentPadding: const EdgeInsets.symmetric(
        horizontal: AppSpacing.md,
        vertical: AppSpacing.sm,
      ),
      leading: Avatar(imageUrl: avatarUrl, name: customerName),
      title: Row(
        children: [
          Expanded(
            child: Text(
              customerName,
              style: AppTypography.bodyMedium.copyWith(
                color: unreadCount > 0 ? AppColors.textPrimary : AppColors.textSecondary,
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
          if (orderNumber != null) ...[
            const SizedBox(width: AppSpacing.xs),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
              decoration: BoxDecoration(
                color: AppColors.accentLight,
                borderRadius: BorderRadius.circular(6),
              ),
              child: Text(
                orderNumber!,
                style: AppTypography.caption.copyWith(
                  color: AppColors.primary,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
          ],
        ],
      ),
      subtitle: Row(
        children: [
          Expanded(
            child: Text(
              lastMessage,
              style: AppTypography.body.copyWith(
                color: AppColors.textSecondary,
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
          const SizedBox(width: AppSpacing.sm),
          if (!isOpen)
            Container(
              width: 8,
              height: 8,
              decoration: const BoxDecoration(
                color: AppColors.textDisabled,
                shape: BoxShape.circle,
              ),
            ),
        ],
      ),
      trailing: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Text(
            time,
            style: AppTypography.caption.copyWith(
              color: unreadCount > 0 ? AppColors.accent : AppColors.textSecondary,
            ),
          ),
          if (unreadCount > 0) ...[
            const SizedBox(height: 4),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
              decoration: BoxDecoration(
                color: AppColors.error,
                borderRadius: BorderRadius.circular(10),
              ),
              constraints: const BoxConstraints(minWidth: 18),
              child: Text(
                unreadCount > 99 ? '99+' : '$unreadCount',
                style: AppTypography.caption.copyWith(
                  color: Colors.white,
                  fontWeight: FontWeight.w600,
                ),
                textAlign: TextAlign.center,
              ),
            ),
          ],
        ],
      ),
    );
  }
}

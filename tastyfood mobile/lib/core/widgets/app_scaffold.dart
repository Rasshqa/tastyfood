import 'package:flutter/material.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';

/// Main app scaffold with optional bottom navigation.
class AppScaffold extends StatelessWidget {
  final String? title;
  final Widget? body;
  final Widget? floatingActionButton;
  final List<Widget>? actions;
  final bool showBottomNav;
  final int currentIndex;
  final Function(int)? onNavTap;
  final bool showOfflineBanner;
  final Widget? offlineBanner;

  const AppScaffold({
    super.key,
    this.title,
    this.body,
    this.floatingActionButton,
    this.actions,
    this.showBottomNav = false,
    this.currentIndex = 0,
    this.onNavTap,
    this.showOfflineBanner = false,
    this.offlineBanner,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: title != null
          ? AppBar(
              title: Text(title!),
              actions: actions,
              elevation: 0,
            )
          : null,
      body: Column(
        children: [
          if (showOfflineBanner) offlineBanner ?? const SizedBox.shrink(),
          if (body != null) Expanded(child: body!),
        ],
      ),
      floatingActionButton: floatingActionButton,
      bottomNavigationBar: showBottomNav
          ? AppBottomNavigation(
              currentIndex: currentIndex,
              onTap: onNavTap,
            )
          : null,
    );
  }
}

/// Bottom navigation with 4 tabs.
class AppBottomNavigation extends StatelessWidget {
  final int currentIndex;
  final Function(int)? onTap;

  const AppBottomNavigation({
    super.key,
    this.currentIndex = 0,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        boxShadow: [
          BoxShadow(
            color: AppColors.primaryDark.withValues(alpha: 0.08),
            blurRadius: 16,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.sm, vertical: AppSpacing.xs),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _NavItem(
                icon: Icons.home_outlined,
                activeIcon: Icons.home,
                label: 'Beranda',
                isSelected: currentIndex == 0,
                onTap: () => onTap?.call(0),
              ),
              _NavItem(
                icon: Icons.receipt_long_outlined,
                activeIcon: Icons.receipt_long,
                label: 'Pesanan',
                isSelected: currentIndex == 1,
                onTap: () => onTap?.call(1),
              ),
              _NavItem(
                icon: Icons.chat_bubble_outline,
                activeIcon: Icons.chat_bubble,
                label: 'Chat',
                isSelected: currentIndex == 2,
                onTap: () => onTap?.call(2),
              ),
              _NavItem(
                icon: Icons.more_horiz,
                activeIcon: Icons.more_horiz,
                label: 'Lainnya',
                isSelected: currentIndex == 3,
                onTap: () => onTap?.call(3),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _NavItem extends StatelessWidget {
  final IconData icon;
  final IconData activeIcon;
  final String label;
  final bool isSelected;
  final VoidCallback? onTap;

  const _NavItem({
    required this.icon,
    required this.activeIcon,
    required this.label,
    required this.isSelected,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      behavior: HitTestBehavior.opaque,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              isSelected ? activeIcon : icon,
              size: 24,
              color: isSelected ? AppColors.primary : AppColors.textSecondary,
            ),
            const SizedBox(height: 4),
            Text(
              label,
              style: TextStyle(
                fontSize: 11,
                fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
                color: isSelected ? AppColors.primary : AppColors.textSecondary,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

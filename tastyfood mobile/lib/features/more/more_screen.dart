import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';
import '../../app/theme/app_typography.dart';
import '../../core/widgets/widgets.dart';
import '../auth/auth_bloc.dart';

class MoreScreen extends StatelessWidget {
  const MoreScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Menu Lainnya',
      showBottomNav: true,
      currentIndex: 3,
      onNavTap: (index) {
        switch (index) {
          case 0:
            context.go('/dashboard');
            break;
          case 1:
            context.go('/orders');
            break;
          case 2:
            context.go('/chat');
            break;
          case 3:
            break;
        }
      },
      body: ListView(
        padding: const EdgeInsets.all(AppSpacing.md),
        children: [
          // Admin Profile Card
          _buildAdminProfileCard(context),
          const SizedBox(height: AppSpacing.lg),

          // Management Section
          Text('Operasional Restoran', style: AppTypography.label),
          const SizedBox(height: AppSpacing.xs),
          _buildHubTile(
            context,
            icon: Icons.verified_outlined,
            iconColor: AppColors.warning,
            title: 'Verifikasi Pembayaran',
            subtitle: 'Antrean konfirmasi transfer bank & bukti bayar',
            onTap: () => context.push('/payments'),
          ),
          const SizedBox(height: AppSpacing.sm),
          _buildHubTile(
            context,
            icon: Icons.restaurant_menu,
            iconColor: AppColors.primary,
            title: 'Kelola Menu Masakan',
            subtitle: 'Tambah, ubah harga, foto, dan ketersediaan menu',
            onTap: () => context.push('/menus'),
          ),
          const SizedBox(height: AppSpacing.sm),
          _buildHubTile(
            context,
            icon: Icons.mail_outline,
            iconColor: AppColors.accentOrange,
            title: 'Pesan Kontak Pelanggan',
            subtitle: 'Kotak masuk pertanyaan dan kritik saran',
            onTap: () => context.push('/messages'),
          ),

          const SizedBox(height: AppSpacing.lg),

          // System Section
          Text('Sistem & Pengaturan', style: AppTypography.label),
          const SizedBox(height: AppSpacing.xs),
          _buildHubTile(
            context,
            icon: Icons.storefront_outlined,
            iconColor: AppColors.primaryDark,
            title: 'Pengaturan Restoran',
            subtitle: 'Informasi resto, jam buka, dan tarif pengiriman',
            onTap: () => context.push('/settings'),
          ),
          const SizedBox(height: AppSpacing.sm),
          _buildHubTile(
            context,
            icon: Icons.info_outline,
            iconColor: AppColors.textSecondary,
            title: 'Tentang Aplikasi',
            subtitle: 'Padang Rice Admin Mobile v1.0.0',
            onTap: () => _showAboutDialog(context),
          ),

          const SizedBox(height: AppSpacing.xl),

          // Logout Button
          OutlinedButton.icon(
            style: OutlinedButton.styleFrom(
              foregroundColor: AppColors.error,
              side: const BorderSide(color: AppColors.error),
              minimumSize: const Size(double.infinity, 50),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            onPressed: () => _confirmLogout(context),
            icon: const Icon(Icons.logout),
            label: const Text('Keluar dari Akun', style: TextStyle(fontWeight: FontWeight.bold)),
          ),
          const SizedBox(height: AppSpacing.xl),
        ],
      ),
    );
  }

  Widget _buildAdminProfileCard(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            AppColors.primaryDark,
            AppColors.primary,
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withValues(alpha: 0.25),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 28,
            backgroundColor: Colors.white.withValues(alpha: 0.2),
            child: const Icon(Icons.admin_panel_settings, size: 32, color: Colors.white),
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Administrator',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Padang Rice Restaurant System',
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: 0.8),
                    fontSize: 13,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildHubTile(
    BuildContext context, {
    required IconData icon,
    required Color iconColor,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: ListTile(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: iconColor.withValues(alpha: 0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: iconColor, size: 24),
        ),
        title: Text(title, style: AppTypography.bodyMedium.copyWith(fontWeight: FontWeight.w600)),
        subtitle: Text(subtitle, style: AppTypography.caption.copyWith(color: AppColors.textSecondary)),
        trailing: const Icon(Icons.chevron_right, color: AppColors.textDisabled),
        onTap: onTap,
      ),
    );
  }

  Future<void> _confirmLogout(BuildContext context) async {
    final confirmed = await ConfirmActionDialog.show(
      context,
      title: 'Konfirmasi Logout',
      message: 'Apakah Anda yakin ingin keluar dari sesi admin ini?',
      confirmLabel: 'Keluar',
      cancelLabel: 'Batal',
      isDestructive: true,
    );

    if (confirmed == true && context.mounted) {
      context.read<AuthBloc>().add(AuthLogoutRequested());
    }
  }

  void _showAboutDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('TastyFood - Padang Rice Admin'),
        content: const Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Versi Aplikasi: 1.0.0+1'),
            SizedBox(height: 8),
            Text('Platform: Flutter Multi-platform'),
            SizedBox(height: 8),
            Text('Backend: Laravel REST API & WebSocket Echo'),
            SizedBox(height: 12),
            Text(
              'Aplikasi pendamping operasional restoran untuk memonitor pesanan, memverifikasi bukti bayar, melayani chat pelanggan, dan mengelola menu.',
              style: TextStyle(fontSize: 12, color: AppColors.textSecondary),
            ),
          ],
        ),
        actions: [
          ElevatedButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('OK'),
          ),
        ],
      ),
    );
  }
}

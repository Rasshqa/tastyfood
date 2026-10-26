import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';
import '../../app/theme/app_typography.dart';
import '../../core/widgets/widgets.dart';
import 'dashboard_bloc.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    context.read<DashboardBloc>().add(DashboardLoadRequested());
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Dashboard Admin',
      showBottomNav: true,
      currentIndex: _currentIndex,
      onNavTap: (index) {
        switch (index) {
          case 0:
            break;
          case 1:
            context.go('/orders');
            break;
          case 2:
            context.go('/chat');
            break;
          case 3:
            context.go('/more');
            break;
        }
      },
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh),
          onPressed: () => context.read<DashboardBloc>().add(DashboardLoadRequested()),
        ),
      ],
      body: BlocBuilder<DashboardBloc, DashboardState>(
        builder: (context, state) {
          if (state is DashboardLoading) {
            return const Center(child: CircularProgressIndicator());
          }
          if (state is DashboardError) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, size: 48, color: AppColors.error),
                  const SizedBox(height: AppSpacing.md),
                  Text(state.message, textAlign: TextAlign.center),
                  const SizedBox(height: AppSpacing.md),
                  ElevatedButton(
                    onPressed: () => context.read<DashboardBloc>().add(DashboardLoadRequested()),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            );
          }
          if (state is DashboardLoaded) {
            return RefreshIndicator(
              onRefresh: () async {
                context.read<DashboardBloc>().add(DashboardLoadRequested());
              },
              child: ListView(
                padding: const EdgeInsets.all(AppSpacing.md),
                children: [
                  // Welcome Banner
                  _buildWelcomeCard(),
                  const SizedBox(height: AppSpacing.lg),

                  // Quick Actions Grid
                  Text('Aksi Cepat', style: AppTypography.h3),
                  const SizedBox(height: AppSpacing.sm),
                  _buildQuickActionsGrid(context),
                  const SizedBox(height: AppSpacing.lg),

                  // Metrics / Statistics
                  Text('Ringkasan Operasional', style: AppTypography.h3),
                  const SizedBox(height: AppSpacing.sm),
                  MetricCard(
                    label: 'Pesan Belum Dibaca',
                    value: state.stats.unreadMessages.toString(),
                    icon: Icons.mail,
                  ),
                  const SizedBox(height: AppSpacing.sm),
                  MetricCard(
                    label: 'Total Galeri Restoran',
                    value: state.stats.totalGalleries.toString(),
                    icon: Icons.photo_library,
                  ),
                  const SizedBox(height: AppSpacing.sm),
                  MetricCard(
                    label: 'Total Berita & Promo',
                    value: state.stats.totalNews.toString(),
                    icon: Icons.article,
                  ),
                  const SizedBox(height: AppSpacing.lg),
                ],
              ),
            );
          }
          return const SizedBox.shrink();
        },
      ),
    );
  }

  Widget _buildWelcomeCard() {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.md),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            AppColors.primary,
            AppColors.primaryDark,
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: AppColors.primary.withValues(alpha: 0.25),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Selamat Datang, Admin!',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Pantau pesanan pelanggan dan verifikasi pembayaran secara langsung.',
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: 0.85),
                    fontSize: 12,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: AppSpacing.sm),
          CircleAvatar(
            radius: 24,
            backgroundColor: Colors.white.withValues(alpha: 0.2),
            child: const Icon(Icons.restaurant, color: Colors.white, size: 28),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActionsGrid(BuildContext context) {
    return GridView.count(
      crossAxisCount: 2,
      crossAxisSpacing: AppSpacing.sm,
      mainAxisSpacing: AppSpacing.sm,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      childAspectRatio: 1.5,
      children: [
        _buildActionTile(
          icon: Icons.receipt_long,
          iconColor: AppColors.primary,
          title: 'Pesanan',
          subtitle: 'Lihat daftar order',
          onTap: () => context.go('/orders'),
        ),
        _buildActionTile(
          icon: Icons.verified_user_outlined,
          iconColor: AppColors.warning,
          title: 'Verifikasi',
          subtitle: 'Bukti transfer',
          onTap: () => context.push('/payments'),
        ),
        _buildActionTile(
          icon: Icons.restaurant_menu,
          iconColor: AppColors.accentOrange,
          title: 'Kelola Menu',
          subtitle: 'Harga & stok',
          onTap: () => context.push('/menus'),
        ),
        _buildActionTile(
          icon: Icons.chat_bubble_outline,
          iconColor: AppColors.success,
          title: 'Chat',
          subtitle: 'Balas pesan',
          onTap: () => context.go('/chat'),
        ),
      ],
    );
  }

  Widget _buildActionTile({
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
      child: InkWell(
        borderRadius: BorderRadius.circular(14),
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.sm),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      color: iconColor.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Icon(icon, color: iconColor, size: 20),
                  ),
                  const Spacer(),
                  const Icon(Icons.arrow_forward_ios, size: 12, color: AppColors.textDisabled),
                ],
              ),
              const SizedBox(height: 8),
              Text(title, style: AppTypography.bodySmall.copyWith(fontWeight: FontWeight.bold)),
              Text(
                subtitle,
                style: AppTypography.caption.copyWith(color: AppColors.textSecondary, fontSize: 10),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

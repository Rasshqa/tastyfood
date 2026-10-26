import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';
import '../../app/theme/app_typography.dart';
import '../../core/models/models.dart';
import '../../core/widgets/widgets.dart';
import 'menu_form_dialog.dart';
import 'menus_bloc.dart';

class MenusScreen extends StatefulWidget {
  const MenusScreen({super.key});

  @override
  State<MenusScreen> createState() => _MenusScreenState();
}

class _MenusScreenState extends State<MenusScreen> {
  final TextEditingController _searchController = TextEditingController();
  String _selectedCategory = 'all';

  final List<Map<String, String>> _categories = [
    {'id': 'all', 'label': 'Semua'},
    {'id': 'nasi', 'label': 'Nasi'},
    {'id': 'lauk', 'label': 'Lauk'},
    {'id': 'sayur', 'label': 'Sayur'},
    {'id': 'minuman', 'label': 'Minuman'},
  ];

  @override
  void initState() {
    super.initState();
    _loadMenus();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  void _loadMenus() {
    context.read<MenusBloc>().add(
          MenusLoadRequested(
            category: _selectedCategory == 'all' ? null : _selectedCategory,
            search: _searchController.text.trim().isEmpty ? null : _searchController.text.trim(),
          ),
        );
  }

  void _showAddDialog() {
    showDialog(
      context: context,
      builder: (_) => MenuFormDialog(
        onSave: ({
          required String name,
          required String description,
          required int price,
          required String category,
          required bool available,
        }) {
          context.read<MenusBloc>().add(
                MenuCreateRequested(
                  name: name,
                  description: description,
                  price: price,
                  category: category,
                  available: available,
                ),
              );
        },
      ),
    );
  }

  void _showEditDialog(Menu menu) {
    showDialog(
      context: context,
      builder: (_) => MenuFormDialog(
        initialMenu: menu,
        onSave: ({
          required String name,
          required String description,
          required int price,
          required String category,
          required bool available,
        }) {
          context.read<MenusBloc>().add(
                MenuUpdateRequested(
                  menuId: menu.id,
                  name: name,
                  description: description,
                  price: price,
                  category: category,
                  available: available,
                ),
              );
        },
      ),
    );
  }

  Future<void> _deleteMenu(Menu menu) async {
    final confirmed = await ConfirmActionDialog.show(
      context,
      title: 'Hapus Menu',
      message: 'Apakah Anda yakin ingin menghapus menu "${menu.name}"? Tindakan ini tidak dapat dibatalkan.',
      confirmLabel: 'Hapus',
      cancelLabel: 'Batal',
      isDestructive: true,
    );

    if (confirmed == true && mounted) {
      context.read<MenusBloc>().add(MenuDeleteRequested(menu.id));
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Kelola Menu',
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _showAddDialog,
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.add),
        label: const Text('Tambah Menu'),
      ),
      body: Column(
        children: [
          // Search Bar
          Padding(
            padding: const EdgeInsets.fromLTRB(AppSpacing.md, AppSpacing.md, AppSpacing.md, AppSpacing.xs),
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Cari menu masakan...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchController.clear();
                          _loadMenus();
                        },
                      )
                    : null,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                contentPadding: const EdgeInsets.symmetric(
                  horizontal: AppSpacing.md,
                  vertical: AppSpacing.sm,
                ),
              ),
              onSubmitted: (_) => _loadMenus(),
            ),
          ),

          // Categories Filter Tabs
          SizedBox(
            height: 48,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 6),
              itemCount: _categories.length,
              separatorBuilder: (_, _) => const SizedBox(width: AppSpacing.xs),
              itemBuilder: (context, index) {
                final cat = _categories[index];
                final isSelected = cat['id'] == _selectedCategory;
                return ChoiceChip(
                  label: Text(cat['label']!),
                  selected: isSelected,
                  selectedColor: AppColors.primary,
                  labelStyle: TextStyle(
                    color: isSelected ? Colors.white : AppColors.textPrimary,
                    fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                  ),
                  onSelected: (selected) {
                    if (selected) {
                      setState(() => _selectedCategory = cat['id']!);
                      _loadMenus();
                    }
                  },
                );
              },
            ),
          ),

          const Divider(height: 8),

          // Menus List
          Expanded(
            child: BlocConsumer<MenusBloc, MenusState>(
              listener: (context, state) {
                if (state is MenusOperationSuccess) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(state.message), backgroundColor: AppColors.success),
                  );
                } else if (state is MenusError) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(state.message), backgroundColor: AppColors.error),
                  );
                }
              },
              builder: (context, state) {
                if (state is MenusLoading) {
                  return const Center(child: CircularProgressIndicator());
                }

                if (state is MenusError) {
                  return Center(
                    child: Padding(
                      padding: const EdgeInsets.all(AppSpacing.lg),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.error_outline, size: 48, color: AppColors.error),
                          const SizedBox(height: AppSpacing.md),
                          Text(state.message, textAlign: TextAlign.center, style: AppTypography.body),
                          const SizedBox(height: AppSpacing.md),
                          ElevatedButton(onPressed: _loadMenus, child: const Text('Coba Lagi')),
                        ],
                      ),
                    ),
                  );
                }

                if (state is MenusLoaded) {
                  final menus = state.menus;
                  if (menus.isEmpty) {
                    return Center(
                      child: Padding(
                        padding: const EdgeInsets.all(AppSpacing.xl),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(Icons.restaurant_menu, size: 56, color: AppColors.textDisabled),
                            const SizedBox(height: AppSpacing.md),
                            Text('Belum ada menu di kategori ini', style: AppTypography.h3),
                            const SizedBox(height: 8),
                            const Text('Gunakan tombol di bawah untuk menambah menu baru.'),
                          ],
                        ),
                      ),
                    );
                  }

                  return RefreshIndicator(
                    onRefresh: () async => _loadMenus(),
                    child: ListView.builder(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      itemCount: menus.length,
                      itemBuilder: (context, index) {
                        final menu = menus[index];
                        return MenuCard(
                          name: menu.name,
                          price: RupiahText.format(menu.price.toDouble()),
                          category: menu.category.toUpperCase(),
                          imageUrl: menu.image,
                          isInStock: menu.available,
                          onAvailabilityChanged: (val) {
                            context.read<MenusBloc>().add(
                                  MenuToggleAvailabilityRequested(menu.id, val),
                                );
                          },
                          onTap: () => _showMenuOptions(menu),
                        );
                      },
                    ),
                  );
                }

                return const SizedBox.shrink();
              },
            ),
          ),
        ],
      ),
    );
  }

  void _showMenuOptions(Menu menu) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (ctx) => SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(
              title: Text(menu.name, style: AppTypography.h3),
              subtitle: Text(
                '${menu.category.toUpperCase()} • ${RupiahText.format(menu.price.toDouble())}',
              ),
            ),
            const Divider(),
            ListTile(
              leading: const Icon(Icons.edit_outlined, color: AppColors.primary),
              title: const Text('Edit Menu'),
              onTap: () {
                Navigator.pop(ctx);
                _showEditDialog(menu);
              },
            ),
            ListTile(
              leading: const Icon(Icons.delete_outline, color: AppColors.error),
              title: const Text('Hapus Menu', style: TextStyle(color: AppColors.error)),
              onTap: () {
                Navigator.pop(ctx);
                _deleteMenu(menu);
              },
            ),
          ],
        ),
      ),
    );
  }
}

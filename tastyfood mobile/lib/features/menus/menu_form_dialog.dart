import 'package:flutter/material.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';
import '../../app/theme/app_typography.dart';
import '../../core/models/models.dart';

class MenuFormDialog extends StatefulWidget {
  final Menu? initialMenu;
  final Function({
    required String name,
    required String description,
    required int price,
    required String category,
    required bool available,
  }) onSave;

  const MenuFormDialog({
    super.key,
    this.initialMenu,
    required this.onSave,
  });

  @override
  State<MenuFormDialog> createState() => _MenuFormDialogState();
}

class _MenuFormDialogState extends State<MenuFormDialog> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _nameController;
  late final TextEditingController _descController;
  late final TextEditingController _priceController;
  late String _category;
  late bool _available;

  final List<String> _categories = ['nasi', 'lauk', 'sayur', 'minuman'];

  @override
  void initState() {
    super.initState();
    final menu = widget.initialMenu;
    _nameController = TextEditingController(text: menu?.name ?? '');
    _descController = TextEditingController(text: menu?.description ?? '');
    _priceController = TextEditingController(text: menu != null ? menu.price.toString() : '');
    _category = menu?.category ?? 'lauk';
    _available = menu?.available ?? true;
  }

  @override
  void dispose() {
    _nameController.dispose();
    _descController.dispose();
    _priceController.dispose();
    super.dispose();
  }

  void _submit() {
    if (_formKey.currentState?.validate() ?? false) {
      final price = int.tryParse(_priceController.text.trim()) ?? 0;
      widget.onSave(
        name: _nameController.text.trim(),
        description: _descController.text.trim(),
        price: price,
        category: _category,
        available: _available,
      );
      Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.initialMenu != null;

    return AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      title: Text(isEditing ? 'Edit Menu' : 'Tambah Menu Baru', style: AppTypography.h3),
      content: SingleChildScrollView(
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              TextFormField(
                controller: _nameController,
                decoration: const InputDecoration(
                  labelText: 'Nama Menu *',
                  hintText: 'Contoh: Rendang Sapi Spesial',
                  border: OutlineInputBorder(),
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'Nama menu wajib diisi';
                  }
                  return null;
                },
              ),
              const SizedBox(height: AppSpacing.md),
              DropdownButtonFormField<String>(
                initialValue: _category,
                decoration: const InputDecoration(
                  labelText: 'Kategori *',
                  border: OutlineInputBorder(),
                ),
                items: _categories.map((c) {
                  return DropdownMenuItem(
                    value: c,
                    child: Text(c[0].toUpperCase() + c.substring(1)),
                  );
                }).toList(),
                onChanged: (val) {
                  if (val != null) {
                    setState(() => _category = val);
                  }
                },
              ),
              const SizedBox(height: AppSpacing.md),
              TextFormField(
                controller: _priceController,
                keyboardType: TextInputType.number,
                decoration: const InputDecoration(
                  labelText: 'Harga (Rp) *',
                  prefixText: 'Rp ',
                  hintText: '25000',
                  border: OutlineInputBorder(),
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'Harga wajib diisi';
                  }
                  if (int.tryParse(val.trim()) == null) {
                    return 'Harga harus berupa angka';
                  }
                  return null;
                },
              ),
              const SizedBox(height: AppSpacing.md),
              TextFormField(
                controller: _descController,
                maxLines: 3,
                decoration: const InputDecoration(
                  labelText: 'Deskripsi Menu',
                  hintText: 'Keterangan bahan atau cita rasa masakan...',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: AppSpacing.md),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text('Tersedia untuk Dipesan', style: AppTypography.bodySmall),
                  Switch(
                    value: _available,
                    activeThumbColor: AppColors.success,
                    onChanged: (val) => setState(() => _available = val),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('Batal'),
        ),
        ElevatedButton(
          onPressed: _submit,
          child: Text(isEditing ? 'Simpan' : 'Tambahkan'),
        ),
      ],
    );
  }
}

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';
import '../../app/theme/app_typography.dart';
import '../../core/widgets/widgets.dart';
import 'settings_repository.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  final _formKey = GlobalKey<FormState>();

  final _nameController = TextEditingController();
  final _addressController = TextEditingController();
  final _phoneController = TextEditingController();
  final _emailController = TextEditingController();
  final _hoursController = TextEditingController();
  final _baseFeeController = TextEditingController();
  final _perKmFeeController = TextEditingController();
  final _maxRadiusController = TextEditingController();

  bool _isLoading = true;
  bool _isSaving = false;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _loadSettings();
  }

  @override
  void dispose() {
    _nameController.dispose();
    _addressController.dispose();
    _phoneController.dispose();
    _emailController.dispose();
    _hoursController.dispose();
    _baseFeeController.dispose();
    _perKmFeeController.dispose();
    _maxRadiusController.dispose();
    super.dispose();
  }

  Future<void> _loadSettings() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final repository = context.read<SettingsRepository>();
      final data = await repository.getSettings();
      if (mounted) {
        _nameController.text = data['restaurant_name']?.toString() ?? 'Padang Rice';
        _addressController.text = data['restaurant_address']?.toString() ?? '';
        _phoneController.text = data['restaurant_phone']?.toString() ?? '';
        _emailController.text = data['restaurant_email']?.toString() ?? '';
        _hoursController.text = data['opening_hours']?.toString() ?? '08:00 - 22:00';
        _baseFeeController.text = data['delivery_base_fee']?.toString() ?? '5000';
        _perKmFeeController.text = data['delivery_per_km']?.toString() ?? '2000';
        _maxRadiusController.text = data['max_delivery_radius']?.toString() ?? '10';

        setState(() {
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _errorMessage = e.toString().replaceAll('Exception: ', '');
          _isLoading = false;
        });
      }
    }
  }

  Future<void> _saveSettings() async {
    if (!(_formKey.currentState?.validate() ?? false)) return;

    setState(() => _isSaving = true);

    try {
      final repository = context.read<SettingsRepository>();
      await repository.updateSettings({
        'restaurant_name': _nameController.text.trim(),
        'restaurant_address': _addressController.text.trim(),
        'restaurant_phone': _phoneController.text.trim(),
        'restaurant_email': _emailController.text.trim(),
        'opening_hours': _hoursController.text.trim(),
        'delivery_base_fee': _baseFeeController.text.trim(),
        'delivery_per_km': _perKmFeeController.text.trim(),
        'max_delivery_radius': _maxRadiusController.text.trim(),
      });

      if (mounted) {
        setState(() => _isSaving = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Pengaturan berhasil disimpan'),
            backgroundColor: AppColors.success,
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isSaving = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(e.toString().replaceAll('Exception: ', '')),
            backgroundColor: AppColors.error,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Pengaturan Restoran',
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh),
          onPressed: _isLoading || _isSaving ? null : _loadSettings,
        ),
      ],
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_errorMessage != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 48, color: AppColors.error),
              const SizedBox(height: AppSpacing.md),
              Text(_errorMessage!, textAlign: TextAlign.center, style: AppTypography.body),
              const SizedBox(height: AppSpacing.md),
              ElevatedButton(onPressed: _loadSettings, child: const Text('Coba Lagi')),
            ],
          ),
        ),
      );
    }

    return Form(
      key: _formKey,
      child: ListView(
        padding: const EdgeInsets.all(AppSpacing.md),
        children: [
          // Section 1: Profil Restoran
          _buildSectionHeader('Profil & Kontak Restoran', Icons.storefront),
          const SizedBox(height: AppSpacing.sm),
          _buildTextField(
            controller: _nameController,
            label: 'Nama Restoran',
            hint: 'Padang Rice',
            required: true,
          ),
          const SizedBox(height: AppSpacing.md),
          _buildTextField(
            controller: _addressController,
            label: 'Alamat Restoran',
            hint: 'Jl. Sudirman No. 45, Jakarta',
            maxLines: 2,
          ),
          const SizedBox(height: AppSpacing.md),
          _buildTextField(
            controller: _phoneController,
            label: 'Nomor Telepon Restoran',
            hint: '021-12345678',
            keyboardType: TextInputType.phone,
          ),
          const SizedBox(height: AppSpacing.md),
          _buildTextField(
            controller: _emailController,
            label: 'Email Kontak',
            hint: 'info@padangrice.com',
            keyboardType: TextInputType.emailAddress,
          ),
          const SizedBox(height: AppSpacing.md),
          _buildTextField(
            controller: _hoursController,
            label: 'Jam Operasional',
            hint: '08:00 - 22:00',
          ),

          const SizedBox(height: AppSpacing.xl),

          // Section 2: Konfigurasi Pengiriman
          _buildSectionHeader('Konfigurasi Pengantaran (Delivery)', Icons.delivery_dining),
          const SizedBox(height: AppSpacing.sm),
          _buildTextField(
            controller: _baseFeeController,
            label: 'Tarif Dasar Pengantaran (Rp)',
            hint: '5000',
            keyboardType: TextInputType.number,
            prefixText: 'Rp ',
          ),
          const SizedBox(height: AppSpacing.md),
          _buildTextField(
            controller: _perKmFeeController,
            label: 'Tarif Tambahan per KM (Rp)',
            hint: '2000',
            keyboardType: TextInputType.number,
            prefixText: 'Rp ',
          ),
          const SizedBox(height: AppSpacing.md),
          _buildTextField(
            controller: _maxRadiusController,
            label: 'Radius Maksimal Pengantaran (KM)',
            hint: '10',
            keyboardType: TextInputType.number,
            suffixText: 'KM',
          ),

          const SizedBox(height: AppSpacing.xl),

          // Save Button
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primary,
              foregroundColor: Colors.white,
              minimumSize: const Size(double.infinity, 50),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            onPressed: _isSaving ? null : _saveSettings,
            child: _isSaving
                ? const SizedBox(
                    width: 24,
                    height: 24,
                    child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                  )
                : const Text('Simpan Pengaturan', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
          ),
          const SizedBox(height: AppSpacing.xl),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 22, color: AppColors.primary),
        const SizedBox(width: 8),
        Text(title, style: AppTypography.h3),
      ],
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    String? hint,
    int maxLines = 1,
    TextInputType? keyboardType,
    String? prefixText,
    String? suffixText,
    bool required = false,
  }) {
    return TextFormField(
      controller: controller,
      maxLines: maxLines,
      keyboardType: keyboardType,
      decoration: InputDecoration(
        labelText: label,
        hintText: hint,
        prefixText: prefixText,
        suffixText: suffixText,
        filled: true,
        fillColor: AppColors.surface,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
      ),
      validator: required
          ? (v) => (v == null || v.trim().isEmpty) ? '$label wajib diisi' : null
          : null,
    );
  }
}

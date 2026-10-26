import 'package:flutter/material.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_typography.dart';
import '../../app/theme/app_spacing.dart';

/// Confirmation dialog for critical actions.
///
/// Used for approve/reject payment, cancel order, delete menu, and logout.
class ConfirmActionDialog extends StatelessWidget {
  final String title;
  final String message;
  final String confirmLabel;
  final String cancelLabel;
  final bool isDestructive;
  final String? textFieldLabel;
  final String? textFieldHint;
  final int? minLength;

  const ConfirmActionDialog({
    super.key,
    required this.title,
    required this.message,
    this.confirmLabel = 'Konfirmasi',
    this.cancelLabel = 'Batal',
    this.isDestructive = false,
    this.textFieldLabel,
    this.textFieldHint,
    this.minLength,
  });

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.medium),
      ),
      title: Text(title, style: AppTypography.h3),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            message,
            style: AppTypography.body.copyWith(color: AppColors.textSecondary),
          ),
          if (textFieldLabel != null) ...[
            const SizedBox(height: AppSpacing.md),
            Text(textFieldLabel!, style: AppTypography.label),
            const SizedBox(height: AppSpacing.sm),
            TextField(
              maxLines: 3,
              decoration: InputDecoration(
                hintText: textFieldHint,
                filled: true,
                fillColor: AppColors.background,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: AppColors.border),
                ),
              ),
            ),
            if (minLength != null)
              Padding(
                padding: const EdgeInsets.only(top: AppSpacing.xs),
                child: Text(
                  'Minimal $minLength karakter',
                  style: AppTypography.caption.copyWith(
                    color: AppColors.textSecondary,
                  ),
                ),
              ),
          ],
        ],
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context, false),
          child: Text(
            cancelLabel,
            style: AppTypography.button.copyWith(color: AppColors.textSecondary),
          ),
        ),
        if (isDestructive)
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.error,
            ),
            onPressed: () => Navigator.pop(context, true),
            child: Text(confirmLabel),
          )
        else
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            child: Text(confirmLabel),
          ),
      ],
    );
  }

  /// Helper to show the dialog and return the result.
  static Future<bool?> show(
    BuildContext context, {
    required String title,
    required String message,
    String confirmLabel = 'Konfirmasi',
    String cancelLabel = 'Batal',
    bool isDestructive = false,
  }) {
    return showDialog<bool>(
      context: context,
      builder: (_) => ConfirmActionDialog(
        title: title,
        message: message,
        confirmLabel: confirmLabel,
        cancelLabel: cancelLabel,
        isDestructive: isDestructive,
      ),
    );
  }

  /// Helper to show an input prompt dialog with optional templates and return the text entered.
  static Future<String?> prompt(
    BuildContext context, {
    required String title,
    required String message,
    String confirmLabel = 'Kirim',
    String cancelLabel = 'Batal',
    bool isDestructive = false,
    required String textFieldLabel,
    String? textFieldHint,
    int? minLength,
    List<String>? templates,
  }) {
    return showDialog<String>(
      context: context,
      builder: (ctx) {
        final controller = TextEditingController();
        String? errorText;

        return StatefulBuilder(
          builder: (context, setState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(AppRadius.medium),
              ),
              title: Text(title, style: AppTypography.h3),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      message,
                      style: AppTypography.body.copyWith(color: AppColors.textSecondary),
                    ),
                    if (templates != null && templates.isNotEmpty) ...[
                      const SizedBox(height: AppSpacing.sm),
                      Text('Pilih Alasan Cepat:', style: AppTypography.label),
                      const SizedBox(height: AppSpacing.xs),
                      Wrap(
                        spacing: 6,
                        runSpacing: 4,
                        children: templates.map((tmpl) {
                          return ActionChip(
                            label: Text(
                              tmpl,
                              style: const TextStyle(fontSize: 11),
                            ),
                            onPressed: () {
                              setState(() {
                                controller.text = tmpl;
                                errorText = null;
                              });
                            },
                          );
                        }).toList(),
                      ),
                    ],
                    const SizedBox(height: AppSpacing.md),
                    Text(textFieldLabel, style: AppTypography.label),
                    const SizedBox(height: AppSpacing.xs),
                    TextField(
                      controller: controller,
                      maxLines: 3,
                      onChanged: (_) {
                        if (errorText != null) {
                          setState(() => errorText = null);
                        }
                      },
                      decoration: InputDecoration(
                        hintText: textFieldHint,
                        errorText: errorText,
                        filled: true,
                        fillColor: AppColors.background,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: const BorderSide(color: AppColors.border),
                        ),
                      ),
                    ),
                    if (minLength != null)
                      Padding(
                        padding: const EdgeInsets.only(top: AppSpacing.xs),
                        child: Text(
                          'Minimal $minLength karakter',
                          style: AppTypography.caption.copyWith(
                            color: AppColors.textSecondary,
                          ),
                        ),
                      ),
                  ],
                ),
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(ctx, null),
                  child: Text(
                    cancelLabel,
                    style: AppTypography.button.copyWith(color: AppColors.textSecondary),
                  ),
                ),
                ElevatedButton(
                  style: isDestructive
                      ? ElevatedButton.styleFrom(backgroundColor: AppColors.error)
                      : null,
                  onPressed: () {
                    final text = controller.text.trim();
                    if (minLength != null && text.length < minLength) {
                      setState(() {
                        errorText = 'Minimal $minLength karakter diperlukan';
                      });
                      return;
                    }
                    if (text.isEmpty) {
                      setState(() {
                        errorText = 'Tidak boleh kosong';
                      });
                      return;
                    }
                    Navigator.pop(ctx, text);
                  },
                  child: Text(confirmLabel),
                ),
              ],
            );
          },
        );
      },
    );
  }
}

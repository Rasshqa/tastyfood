import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../app/theme/app_colors.dart';
import '../../app/theme/app_spacing.dart';
import '../../app/theme/app_typography.dart';
import '../../core/models/models.dart';
import '../../core/utils/date_formatter.dart';
import '../../core/widgets/widgets.dart';
import 'messages_repository.dart';

class MessagesScreen extends StatefulWidget {
  const MessagesScreen({super.key});

  @override
  State<MessagesScreen> createState() => _MessagesScreenState();
}

class _MessagesScreenState extends State<MessagesScreen> {
  List<ContactMessage> _messages = [];
  bool _isLoading = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _loadMessages();
  }

  Future<void> _loadMessages() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final repository = context.read<MessagesRepository>();
      final list = await repository.getMessages();
      if (mounted) {
        setState(() {
          _messages = list;
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

  Future<void> _markRead(ContactMessage msg) async {
    if (msg.isRead) return;
    try {
      await context.read<MessagesRepository>().markAsRead(msg.id);
      _loadMessages();
    } catch (_) {}
  }

  Future<void> _deleteMessage(ContactMessage msg) async {
    final confirmed = await ConfirmActionDialog.show(
      context,
      title: 'Hapus Pesan',
      message: 'Hapus pesan dari "${msg.name}"? Tindakan ini tidak dapat dibatalkan.',
      confirmLabel: 'Hapus',
      cancelLabel: 'Batal',
      isDestructive: true,
    );

    if (confirmed == true && mounted) {
      try {
        await context.read<MessagesRepository>().deleteMessage(msg.id);
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Pesan berhasil dihapus'), backgroundColor: AppColors.success),
          );
          _loadMessages();
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(e.toString()), backgroundColor: AppColors.error),
          );
        }
      }
    }
  }

  void _showMessageDetail(ContactMessage msg) {
    _markRead(msg);

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text(msg.subject, style: AppTypography.h3),
                  ),
                  IconButton(
                    icon: const Icon(Icons.delete_outline, color: AppColors.error),
                    tooltip: 'Hapus Pesan',
                    onPressed: () {
                      Navigator.pop(ctx);
                      _deleteMessage(msg);
                    },
                  ),
                ],
              ),
              const SizedBox(height: 4),
              Text(
                'Dari: ${msg.name} (${msg.email})',
                style: AppTypography.caption.copyWith(color: AppColors.primary),
              ),
              Text(
                DateFormatter.formatDateTime(msg.createdAt),
                style: AppTypography.caption.copyWith(color: AppColors.textSecondary),
              ),
              const Divider(height: 24),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(AppSpacing.md),
                decoration: BoxDecoration(
                  color: AppColors.background,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.border),
                ),
                child: Text(
                  msg.message,
                  style: AppTypography.body,
                ),
              ),
              const SizedBox(height: AppSpacing.lg),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () {
                        Clipboard.setData(ClipboardData(text: msg.email));
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Email disalin ke clipboard')),
                        );
                      },
                      icon: const Icon(Icons.copy, size: 18),
                      label: const Text('Salin Email'),
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () => Navigator.pop(ctx),
                      child: const Text('Tutup'),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Pesan Pelanggan',
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh),
          onPressed: _loadMessages,
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
              ElevatedButton(onPressed: _loadMessages, child: const Text('Coba Lagi')),
            ],
          ),
        ),
      );
    }

    if (_messages.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.xl),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.mail_outline, size: 56, color: AppColors.textDisabled),
              const SizedBox(height: AppSpacing.md),
              Text('Kotak Pesan Bersih', style: AppTypography.h3),
              const SizedBox(height: 8),
              const Text('Belum ada pesan kontak dari pelanggan masuk.'),
            ],
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _loadMessages,
      child: ListView.separated(
        padding: const EdgeInsets.all(AppSpacing.md),
        itemCount: _messages.length,
        separatorBuilder: (_, _) => const SizedBox(height: AppSpacing.sm),
        itemBuilder: (context, index) {
          final msg = _messages[index];
          return Container(
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: msg.isRead ? AppColors.border : AppColors.primary.withValues(alpha: 0.5),
                width: msg.isRead ? 1 : 1.5,
              ),
            ),
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 4),
              leading: CircleAvatar(
                backgroundColor: msg.isRead
                    ? AppColors.background
                    : AppColors.primary.withValues(alpha: 0.1),
                child: Icon(
                  msg.isRead ? Icons.mail_outline : Icons.mark_email_unread,
                  color: msg.isRead ? AppColors.textSecondary : AppColors.primary,
                ),
              ),
              title: Row(
                children: [
                  Expanded(
                    child: Text(
                      msg.subject,
                      style: AppTypography.bodyMedium.copyWith(
                        fontWeight: msg.isRead ? FontWeight.normal : FontWeight.bold,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  if (!msg.isRead)
                    Container(
                      width: 8,
                      height: 8,
                      decoration: const BoxDecoration(
                        color: AppColors.primary,
                        shape: BoxShape.circle,
                      ),
                    ),
                ],
              ),
              subtitle: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 2),
                  Text(
                    '${msg.name} • ${msg.email}',
                    style: AppTypography.caption.copyWith(color: AppColors.textSecondary),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 2),
                  Text(
                    msg.message,
                    style: AppTypography.caption,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
              onTap: () => _showMessageDetail(msg),
            ),
          );
        },
      ),
    );
  }
}

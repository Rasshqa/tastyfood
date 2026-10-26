import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';
import '../../core/widgets/widgets.dart';
import '../../app/theme/app_spacing.dart';
import '../../core/utils/date_formatter.dart';
import 'chat_bloc.dart';

class ChatScreen extends StatefulWidget {
  const ChatScreen({super.key});

  @override
  State<ChatScreen> createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  @override
  void initState() {
    super.initState();
    context.read<ChatBloc>().add(ChatConversationsLoadRequested());
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      title: 'Chat',
      showBottomNav: true,
      currentIndex: 2,
      onNavTap: (index) {
        switch (index) {
          case 0:
            context.go('/dashboard');
            break;
          case 1:
            context.go('/orders');
            break;
          case 2:
            break;
          case 3:
            context.go('/more');
            break;
        }
      },
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh),
          onPressed: () => context.read<ChatBloc>().add(ChatConversationsLoadRequested()),
        ),
      ],
      body: BlocBuilder<ChatBloc, ChatState>(
        builder: (context, state) {
          if (state is ChatLoading) {
            return const Center(child: CircularProgressIndicator());
          }
          if (state is ChatError) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(state.message),
                  TextButton(
                    onPressed: () => context.read<ChatBloc>().add(ChatConversationsLoadRequested()),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            );
          }
          if (state is ChatConversationsLoaded) {
            return state.conversations.isEmpty
                ? const Center(child: Text('Belum ada percakapan'))
                : ListView.builder(
                    padding: const EdgeInsets.all(AppSpacing.sm),
                    itemCount: state.conversations.length,
                    itemBuilder: (context, index) {
                      final conv = state.conversations[index];
                      return ConversationTile(
                        customerName: conv.user?.name ?? 'Unknown',
                        lastMessage: conv.latestMessage?.message ?? '',
                        time: DateFormatter.timeAgo(conv.lastMessageAt),
                        unreadCount: conv.unreadCount,
                        orderNumber: conv.orderId?.toString(),
                        isOpen: conv.status == 'open',
                        onTap: () => context.push('/chat/${conv.id}'),
                      );
                    },
                  );
          }
          return const SizedBox.shrink();
        },
      ),
    );
  }
}

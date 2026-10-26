import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../../core/models/chat.dart';
import 'chat_repository.dart';

// Events
abstract class ChatEvent extends Equatable {
  @override
  List<Object?> get props => [];
}

class ChatConversationsLoadRequested extends ChatEvent {}

class ChatMessagesLoadRequested extends ChatEvent {
  final int conversationId;
  final int page;

  ChatMessagesLoadRequested(this.conversationId, {this.page = 1});

  @override
  List<Object?> get props => [conversationId, page];
}

class ChatMessageSendRequested extends ChatEvent {
  final int conversationId;
  final String message;

  ChatMessageSendRequested(this.conversationId, this.message);

  @override
  List<Object?> get props => [conversationId, message];
}

class ChatMarkAsReadRequested extends ChatEvent {
  final int conversationId;

  ChatMarkAsReadRequested(this.conversationId);

  @override
  List<Object?> get props => [conversationId];
}

class ChatTotalUnreadRequested extends ChatEvent {}

// States
abstract class ChatState extends Equatable {
  @override
  List<Object?> get props => [];
}

class ChatInitial extends ChatState {}

class ChatLoading extends ChatState {}

class ChatConversationsLoaded extends ChatState {
  final List<Conversation> conversations;

  ChatConversationsLoaded(this.conversations);

  @override
  List<Object?> get props => [conversations];
}

class ChatMessagesLoaded extends ChatState {
  final int conversationId;
  final List<Message> messages;
  final bool hasMore;

  ChatMessagesLoaded(this.conversationId, this.messages, {this.hasMore = true});

  @override
  List<Object?> get props => [conversationId, messages, hasMore];
}

class ChatError extends ChatState {
  final String message;

  ChatError(this.message);

  @override
  List<Object?> get props => [message];
}

class ChatUnreadCount extends ChatState {
  final int count;

  ChatUnreadCount(this.count);

  @override
  List<Object?> get props => [count];
}

// Bloc
class ChatBloc extends Bloc<ChatEvent, ChatState> {
  final ChatRepository repository;

  ChatBloc(this.repository) : super(ChatInitial()) {
    on<ChatConversationsLoadRequested>(_onConversationsLoadRequested);
    on<ChatMessagesLoadRequested>(_onMessagesLoadRequested);
    on<ChatMessageSendRequested>(_onMessageSendRequested);
    on<ChatMarkAsReadRequested>(_onMarkAsReadRequested);
    on<ChatTotalUnreadRequested>(_onTotalUnreadRequested);
  }

  Future<void> _onConversationsLoadRequested(
    ChatConversationsLoadRequested event,
    Emitter<ChatState> emit,
  ) async {
    emit(ChatLoading());
    try {
      final conversations = await repository.getConversations();
      emit(ChatConversationsLoaded(conversations));
    } catch (e) {
      emit(ChatError(e.toString()));
    }
  }

  Future<void> _onMessagesLoadRequested(
    ChatMessagesLoadRequested event,
    Emitter<ChatState> emit,
  ) async {
    try {
      final messages = await repository.getMessages(
        event.conversationId,
        page: event.page,
      );
      emit(ChatMessagesLoaded(
        event.conversationId,
        messages,
        hasMore: event.page == 1,
      ));
    } catch (e) {
      emit(ChatError(e.toString()));
    }
  }

  Future<void> _onMessageSendRequested(
    ChatMessageSendRequested event,
    Emitter<ChatState> emit,
  ) async {
    try {
      await repository.sendMessage(event.conversationId, event.message);
      emit(ChatMessagesLoaded(
        event.conversationId,
        [
          Message(
            id: 0,
            uuid: '',
            conversationId: event.conversationId,
            senderId: 0,
            senderType: 'admin',
            message: event.message,
            messageType: 'text',
            isRead: false,
            createdAt: DateTime.now(),
          ),
        ],
        hasMore: false,
      ));
    } catch (e) {
      emit(ChatError(e.toString()));
    }
  }

  Future<void> _onMarkAsReadRequested(
    ChatMarkAsReadRequested event,
    Emitter<ChatState> emit,
  ) async {
    try {
      await repository.markAsRead(event.conversationId);
      add(ChatConversationsLoadRequested());
    } catch (e) {
      emit(ChatError(e.toString()));
    }
  }

  Future<void> _onTotalUnreadRequested(
    ChatTotalUnreadRequested event,
    Emitter<ChatState> emit,
  ) async {
    try {
      final count = await repository.getTotalUnread();
      emit(ChatUnreadCount(count));
    } catch (e) {
      emit(ChatError(e.toString()));
    }
  }
}

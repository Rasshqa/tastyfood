class Conversation {
  final int id;
  final int userId;
  final int? orderId;
  final String status;
  final DateTime lastMessageAt;
  final int unreadCount;
  final User? user;
  final Message? latestMessage;

  Conversation({
    required this.id,
    required this.userId,
    this.orderId,
    required this.status,
    required this.lastMessageAt,
    this.unreadCount = 0,
    this.user,
    this.latestMessage,
  });

  factory Conversation.fromJson(Map<String, dynamic> json) => Conversation(
        id: json['id'],
        userId: json['user_id'],
        orderId: json['order_id'],
        status: json['status'],
        lastMessageAt: DateTime.parse(json['last_message_at']),
        unreadCount: json['unread_count'] ?? 0,
        user: json['user'] != null ? User.fromJson(json['user']) : null,
        latestMessage: json['latest_message'] != null
            ? Message.fromJson(json['latest_message'])
            : null,
      );
}

class Message {
  final int id;
  final String uuid;
  final int conversationId;
  final int senderId;
  final String senderType;
  final String message;
  final String messageType;
  final bool isRead;
  final DateTime? readAt;
  final DateTime createdAt;

  Message({
    required this.id,
    required this.uuid,
    required this.conversationId,
    required this.senderId,
    required this.senderType,
    required this.message,
    required this.messageType,
    required this.isRead,
    this.readAt,
    required this.createdAt,
  });

  factory Message.fromJson(Map<String, dynamic> json) => Message(
        id: json['id'],
        uuid: json['uuid'],
        conversationId: json['conversation_id'],
        senderId: json['sender_id'],
        senderType: json['sender_type'],
        message: json['message'],
        messageType: json['message_type'],
        isRead: json['is_read'] ?? false,
        readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
        createdAt: DateTime.parse(json['created_at']),
      );
}

class User {
  final int id;
  final String name;
  final String email;
  final String? phone;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
  });

  factory User.fromJson(Map<String, dynamic> json) => User(
        id: json['id'],
        name: json['name'],
        email: json['email'],
        phone: json['phone'],
      );
}

class Menu {
  final int id;
  final String name;
  final String description;
  final int price;
  final String? image;
  final String category;
  final bool available;

  Menu({
    required this.id,
    required this.name,
    required this.description,
    required this.price,
    this.image,
    required this.category,
    required this.available,
  });

  factory Menu.fromJson(Map<String, dynamic> json) => Menu(
        id: json['id'],
        name: json['name'],
        description: json['description'],
        price: json['price'],
        image: json['image'],
        category: json['category'],
        available: json['available'] ?? true,
      );
}

class ContactMessage {
  final int id;
  final String name;
  final String email;
  final String subject;
  final String message;
  final bool isRead;
  final DateTime createdAt;

  ContactMessage({
    required this.id,
    required this.name,
    required this.email,
    required this.subject,
    required this.message,
    required this.isRead,
    required this.createdAt,
  });

  factory ContactMessage.fromJson(Map<String, dynamic> json) => ContactMessage(
        id: json['id'],
        name: json['name'],
        email: json['email'],
        subject: json['subject'],
        message: json['message'],
        isRead: json['is_read'] ?? false,
        createdAt: DateTime.parse(json['created_at']),
      );
}

class DashboardStats {
  final int totalNews;
  final int totalGalleries;
  final int unreadMessages;

  DashboardStats({
    required this.totalNews,
    required this.totalGalleries,
    required this.unreadMessages,
  });

  factory DashboardStats.fromJson(Map<String, dynamic> json) => DashboardStats(
        totalNews: json['total_news'],
        totalGalleries: json['total_galleries'],
        unreadMessages: json['unread_messages'],
      );
}

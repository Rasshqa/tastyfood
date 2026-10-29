import 'dart:io' show Platform;
import 'package:flutter/foundation.dart' show kIsWeb;

class ApiEndpoints {
  static String get defaultUrl {
    if (kIsWeb) return 'http://localhost:8000';
    try {
      if (Platform.isAndroid) return 'http://10.0.2.2:8000';
    } catch (_) {}
    return 'http://127.0.0.1:8000';
  }

  static String get baseUrl {
    const fromEnv = String.fromEnvironment('API_BASE_URL');
    return fromEnv.isNotEmpty ? fromEnv : defaultUrl;
  }

  // System & Health
  static const String health = '/api/health';

  // Auth
  static const String login = '/admin/login';
  static const String logout = '/admin/logout';

  // Dashboard
  static const String dashboard = '/admin';

  // Orders
  static const String orders = '/admin/pesanan';
  static String orderDetail(int id) => '/admin/pesanan/$id';
  static String updateOrderStatus(int id) => '/admin/pesanan/$id/status';
  static String approvePayment(int id) => '/admin/order-management/payment/$id/approve';
  static String rejectPayment(int id) => '/admin/order-management/payment/$id/reject';

  // Chat
  static const String conversations = '/admin/chat';
  static String conversationDetail(int id) => '/admin/chat/$id';
  static String conversationMessages(int id) => '/admin/chat/$id/messages';
  static String sendMessage(int id) => '/admin/chat/$id/messages';
  static String markAsRead(int id) => '/admin/chat/$id/read';
  static String closeConversation(int id) => '/admin/chat/$id/close';
  static const String chatSearch = '/admin/chat-search';
  static const String chatToggle = '/admin/chat-toggle';
  static const String chatUnread = '/admin/chat-unread';

  // Menus
  static const String menus = '/admin/menus';
  static String menuDetail(int id) => '/admin/menus/$id';

  // Payment Methods
  static const String paymentMethods = '/admin/payment-methods';
  static String paymentMethodDetail(int id) => '/admin/payment-methods/$id';

  // Messages (Contact)
  static const String contactMessages = '/admin/pesan';
  static String contactMessageDetail(int id) => '/admin/pesan/$id';

  // Settings
  static const String settings = '/admin/pengaturan';
}

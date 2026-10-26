import 'package:dio/dio.dart';
import '../../core/api/api_client.dart';
import '../../core/api/api_endpoints.dart';
import '../../core/api/api_exception.dart';
import '../../core/models/chat.dart';

class ChatRepository {
  final ApiClient apiClient;

  ChatRepository(this.apiClient);

  Future<List<Conversation>> getConversations() async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.conversations,
        options: Options(headers: {'Accept': 'application/json'}),
      );

      final data = response.data;
      if (data['success'] == true) {
        return (data['conversations'] as List)
            .map((e) => Conversation.fromJson(e))
            .toList();
      }
      throw ApiException('Gagal memuat percakapan');
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memuat percakapan',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<List<Message>> getMessages(int conversationId, {int page = 1}) async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.conversationMessages(conversationId),
        queryParameters: {'page': page},
      );

      final data = response.data;
      if (data['success'] == true) {
        return (data['messages'] as List)
            .map((e) => Message.fromJson(e))
            .toList();
      }
      throw ApiException('Gagal memuat pesan');
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memuat pesan',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<Message> sendMessage(int conversationId, String message) async {
    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.sendMessage(conversationId),
        data: {'message': message},
      );

      final data = response.data;
      if (data['success'] == true) {
        return Message.fromJson(data['message']);
      }
      throw ApiException('Gagal mengirim pesan');
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal mengirim pesan',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> markAsRead(int conversationId) async {
    try {
      await apiClient.dio.post(ApiEndpoints.markAsRead(conversationId));
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal menandai pesan',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> closeConversation(int conversationId) async {
    try {
      await apiClient.dio.post(ApiEndpoints.closeConversation(conversationId));
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal menutup percakapan',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<int> getTotalUnread() async {
    try {
      final response = await apiClient.dio.get(ApiEndpoints.chatUnread);
      return response.data['unread'] ?? 0;
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memuat notifikasi',
        statusCode: e.response?.statusCode,
      );
    }
  }
}

import 'package:dio/dio.dart';
import '../../core/api/api_client.dart';
import '../../core/api/api_endpoints.dart';
import '../../core/api/api_exception.dart';
import '../../core/models/models.dart';

class MessagesRepository {
  final ApiClient apiClient;

  MessagesRepository(this.apiClient);

  Future<List<ContactMessage>> getMessages() async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.contactMessages,
        options: Options(headers: {'Accept': 'application/json'}),
      );

      final data = response.data;
      if (data is Map<String, dynamic> && data['messages'] != null) {
        return (data['messages'] as List).map((e) => ContactMessage.fromJson(e)).toList();
      } else if (data is List) {
        return data.map((e) => ContactMessage.fromJson(e)).toList();
      }
      return [];
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memuat pesan kontak',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> markAsRead(int messageId) async {
    try {
      await apiClient.dio.patch(
        ApiEndpoints.contactMessageDetail(messageId),
        data: {'is_read': true},
      );
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal menandai pesan telah dibaca',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> deleteMessage(int messageId) async {
    try {
      await apiClient.dio.delete(ApiEndpoints.contactMessageDetail(messageId));
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal menghapus pesan',
        statusCode: e.response?.statusCode,
      );
    }
  }
}

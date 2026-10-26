import 'package:dio/dio.dart';
import '../../core/api/api_client.dart';
import '../../core/api/api_endpoints.dart';
import '../../core/api/api_exception.dart';

class SettingsRepository {
  final ApiClient apiClient;

  SettingsRepository(this.apiClient);

  Future<Map<String, dynamic>> getSettings() async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.settings,
        options: Options(headers: {'Accept': 'application/json'}),
      );

      final data = response.data;
      if (data is Map<String, dynamic> && data['settings'] != null) {
        return Map<String, dynamic>.from(data['settings']);
      } else if (data is Map<String, dynamic>) {
        return Map<String, dynamic>.from(data);
      }
      return {};
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memuat pengaturan',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> updateSettings(Map<String, dynamic> settingsData) async {
    try {
      await apiClient.dio.post(
        ApiEndpoints.settings,
        data: settingsData,
        options: Options(headers: {'Accept': 'application/json'}),
      );
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal menyimpan pengaturan',
        statusCode: e.response?.statusCode,
      );
    }
  }
}

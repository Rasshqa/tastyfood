import 'package:dio/dio.dart';
import '../../core/api/api_client.dart';
import '../../core/api/api_endpoints.dart';
import '../../core/api/api_exception.dart';
import '../../core/models/models.dart';

class DashboardRepository {
  final ApiClient apiClient;

  DashboardRepository(this.apiClient);

  Future<DashboardStats> getStats() async {
    try {
      final response = await apiClient.dio.get(
        ApiEndpoints.dashboard,
        options: Options(headers: {'Accept': 'application/json'}),
      );

      final data = response.data;
      if (data['success'] == true) {
        return DashboardStats.fromJson(data['stats']);
      }
      throw ApiException('Gagal memuat statistik');
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memuat statistik',
        statusCode: e.response?.statusCode,
      );
    }
  }
}

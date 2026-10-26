import 'package:dio/dio.dart';
import '../../core/api/api_client.dart';
import '../../core/api/api_endpoints.dart';
import '../../core/api/api_exception.dart';

class AuthRepository {
  final ApiClient apiClient;

  AuthRepository(this.apiClient);

  Future<void> login(String email, String password) async {
    try {
      final response = await apiClient.dio.post(
        ApiEndpoints.login,
        data: {'email': email, 'password': password},
      );

      if (response.statusCode == 200 || response.statusCode == 302) {
        final cookies = response.headers['set-cookie'];
        if (cookies != null && cookies.isNotEmpty) {
          await apiClient.saveToken(cookies.first);
        }
      } else {
        throw ApiException('Login gagal');
      }
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Login gagal',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> logout() async {
    try {
      await apiClient.dio.post(ApiEndpoints.logout);
    } finally {
      await apiClient.clearToken();
    }
  }

  Future<bool> isAuthenticated() async {
    final token = await apiClient.getToken();
    return token != null;
  }
}

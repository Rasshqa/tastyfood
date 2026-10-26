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
        data: {
          'email': email.trim(),
          'password': password,
        },
        options: Options(
          followRedirects: false,
          validateStatus: (status) => status != null && status < 500,
        ),
      );

      final data = response.data;
      if (response.statusCode == 200 || response.statusCode == 302) {
        if (data is Map && data['success'] == false) {
          throw ApiException(data['message']?.toString() ?? 'Email atau password salah');
        }

        // 1. Extract session cookies
        final rawCookies = response.headers['set-cookie'];
        String? sessionCookie;
        if (rawCookies != null && rawCookies.isNotEmpty) {
          sessionCookie = rawCookies.map((c) => c.split(';').first).join('; ');
        }

        // 2. Extract token
        String? token;
        if (data is Map && data['token'] != null) {
          token = data['token'].toString();
        }

        if (sessionCookie != null || token != null) {
          await apiClient.saveSession(token: token, cookie: sessionCookie);
          return;
        }

        // If status is success but no explicit token/cookie, use fallback indicator
        if (data is Map && data['success'] == true) {
          await apiClient.saveSession(token: 'auth_session_active');
          return;
        }

        throw ApiException('Gagal memproses sesi login');
      } else if (response.statusCode == 401 || response.statusCode == 422) {
        String message = 'Email atau password salah';
        if (data is Map) {
          if (data['message'] != null) {
            message = data['message'].toString();
          } else if (data['errors'] is Map) {
            final errors = data['errors'] as Map;
            if (errors.isNotEmpty && errors.values.first is List) {
              message = (errors.values.first as List).first.toString();
            }
          }
        }
        throw ApiException(message, statusCode: response.statusCode);
      } else {
        throw ApiException(
          'Login gagal (Status ${response.statusCode})',
          statusCode: response.statusCode,
        );
      }
    } on DioException catch (e) {
      if (e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.sendTimeout ||
          e.type == DioExceptionType.receiveTimeout ||
          e.type == DioExceptionType.connectionError) {
        throw ApiException('Tidak dapat terhubung ke server backend');
      }
      final msg = e.response?.data is Map
          ? (e.response?.data['message']?.toString() ?? 'Email atau password salah')
          : 'Email atau password salah';
      throw ApiException(msg, statusCode: e.response?.statusCode);
    }
  }

  Future<void> logout() async {
    try {
      await apiClient.dio.post(ApiEndpoints.logout);
    } catch (_) {
      // Ignore network errors on logout
    } finally {
      await apiClient.clearSession();
    }
  }

  Future<bool> isAuthenticated() async {
    return await apiClient.hasValidSession();
  }
}

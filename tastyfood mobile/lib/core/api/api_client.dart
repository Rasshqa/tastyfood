import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiClient {
  final Dio dio;
  final FlutterSecureStorage storage;

  ApiClient({
    required this.dio,
    required this.storage,
  }) {
    dio.options.headers = {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    };
    dio.options.validateStatus = (status) => status != null && status < 500;

    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await storage.read(key: 'auth_token');
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        final cookie = await storage.read(key: 'auth_cookie');
        if (cookie != null && cookie.isNotEmpty) {
          options.headers['Cookie'] = cookie;
        }
        options.headers['Accept'] = 'application/json';
        options.headers['X-Requested-With'] = 'XMLHttpRequest';
        return handler.next(options);
      },
      onError: (error, handler) async {
        if (error.response?.statusCode == 401) {
          await clearSession();
        }
        return handler.next(error);
      },
    ));
  }

  Future<void> saveSession({String? token, String? cookie}) async {
    if (token != null && token.isNotEmpty) {
      await storage.write(key: 'auth_token', value: token);
    }
    if (cookie != null && cookie.isNotEmpty) {
      await storage.write(key: 'auth_cookie', value: cookie);
    }
  }

  Future<void> saveToken(String token) async {
    await storage.write(key: 'auth_token', value: token);
  }

  Future<void> saveCookie(String cookie) async {
    await storage.write(key: 'auth_cookie', value: cookie);
  }

  Future<void> clearSession() async {
    await storage.delete(key: 'auth_token');
    await storage.delete(key: 'auth_cookie');
  }

  Future<void> clearToken() async {
    await clearSession();
  }

  Future<String?> getToken() async {
    return await storage.read(key: 'auth_token');
  }

  Future<String?> getCookie() async {
    return await storage.read(key: 'auth_cookie');
  }

  Future<bool> hasValidSession() async {
    final token = await storage.read(key: 'auth_token');
    final cookie = await storage.read(key: 'auth_cookie');
    return (token != null && token.isNotEmpty) || (cookie != null && cookie.isNotEmpty);
  }
}

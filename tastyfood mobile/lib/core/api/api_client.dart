import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiClient {
  final Dio dio;
  final FlutterSecureStorage storage;
  void Function()? onUnauthorized;
  String? _token;
  String? _cookie;

  ApiClient({
    required this.dio,
    required this.storage,
    this.onUnauthorized,
  }) {
    dio.options.connectTimeout = const Duration(seconds: 15);
    dio.options.receiveTimeout = const Duration(seconds: 15);
    dio.options.sendTimeout = const Duration(seconds: 15);
    dio.options.headers = {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    };
    dio.options.extra['withCredentials'] = true;
    dio.options.validateStatus = (status) => status != null && status < 500;

    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await getToken();
        if (token != null && token.isNotEmpty) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        final cookie = await getCookie();
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
          onUnauthorized?.call();
        }
        return handler.next(error);
      },
    ));
  }

  Future<void> saveSession({String? token, String? cookie}) async {
    if (token != null && token.isNotEmpty) {
      _token = token;
      try {
        await storage.write(key: 'auth_token', value: token);
      } catch (_) {}
    }
    if (cookie != null && cookie.isNotEmpty) {
      _cookie = cookie;
      try {
        await storage.write(key: 'auth_cookie', value: cookie);
      } catch (_) {}
    }
  }

  Future<void> saveToken(String token) async {
    _token = token;
    try {
      await storage.write(key: 'auth_token', value: token);
    } catch (_) {}
  }

  Future<void> saveCookie(String cookie) async {
    _cookie = cookie;
    try {
      await storage.write(key: 'auth_cookie', value: cookie);
    } catch (_) {}
  }

  Future<void> clearSession() async {
    _token = null;
    _cookie = null;
    try {
      await storage.delete(key: 'auth_token');
    } catch (_) {}
    try {
      await storage.delete(key: 'auth_cookie');
    } catch (_) {}
  }

  Future<void> clearToken() async {
    await clearSession();
  }

  Future<String?> getToken() async {
    if (_token != null && _token!.isNotEmpty) return _token;
    try {
      _token = await storage.read(key: 'auth_token');
    } catch (_) {}
    return _token;
  }

  Future<String?> getCookie() async {
    if (_cookie != null && _cookie!.isNotEmpty) return _cookie;
    try {
      _cookie = await storage.read(key: 'auth_cookie');
    } catch (_) {}
    return _cookie;
  }

  Future<bool> hasValidSession() async {
    final token = await getToken();
    final cookie = await getCookie();
    return (token != null && token.isNotEmpty) || (cookie != null && cookie.isNotEmpty);
  }
}

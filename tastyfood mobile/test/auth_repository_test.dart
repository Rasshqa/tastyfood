import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:mocktail/mocktail.dart';
import 'package:tasty_food/core/api/api_client.dart';
import 'package:tasty_food/core/api/api_endpoints.dart';
import 'package:tasty_food/core/api/api_exception.dart';
import 'package:tasty_food/features/auth/auth_repository.dart';

class MockDio extends Mock implements Dio {}
class MockFlutterSecureStorage extends Mock implements FlutterSecureStorage {}

void main() {
  late MockDio mockDio;
  late MockFlutterSecureStorage mockStorage;
  late ApiClient apiClient;
  late AuthRepository authRepository;

  setUp(() {
    mockDio = MockDio();
    mockStorage = MockFlutterSecureStorage();
    when(() => mockDio.options).thenReturn(BaseOptions());
    when(() => mockDio.interceptors).thenReturn(Interceptors());
    apiClient = ApiClient(dio: mockDio, storage: mockStorage);
    authRepository = AuthRepository(apiClient);
  });

  test('login succeeds and stores session token & cookie', () async {
    when(() => mockStorage.write(key: any(named: 'key'), value: any(named: 'value')))
        .thenAnswer((_) async {});

    when(() => mockDio.post(
          ApiEndpoints.login,
          data: {'email': 'admin@padangrice.com', 'password': 'password123'},
          options: any(named: 'options'),
        )).thenAnswer(
      (_) async => Response(
        requestOptions: RequestOptions(path: ApiEndpoints.login),
        statusCode: 200,
        headers: Headers.fromMap({
          'set-cookie': ['laravel_session=session_token_xyz; path=/; httponly'],
        }),
        data: {
          'success': true,
          'token': 'session_token_xyz',
          'admin': {'id': 1, 'email': 'admin@padangrice.com'},
        },
      ),
    );

    await authRepository.login('admin@padangrice.com', 'password123');

    verify(() => mockStorage.write(key: 'auth_token', value: 'session_token_xyz')).called(1);
    verify(() => mockStorage.write(key: 'auth_cookie', value: 'laravel_session=session_token_xyz')).called(1);
  });

  test('login throws ApiException on invalid credentials', () async {
    when(() => mockDio.post(
          ApiEndpoints.login,
          data: {'email': 'admin@padangrice.com', 'password': 'wrong'},
          options: any(named: 'options'),
        )).thenAnswer(
      (_) async => Response(
        requestOptions: RequestOptions(path: ApiEndpoints.login),
        statusCode: 401,
        data: {
          'success': false,
          'message': 'Email atau password salah.',
        },
      ),
    );

    expect(
      () => authRepository.login('admin@padangrice.com', 'wrong'),
      throwsA(isA<ApiException>().having((e) => e.message, 'message', 'Email atau password salah.')),
    );
  });
}

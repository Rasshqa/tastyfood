import 'package:dio/dio.dart';
import '../../core/api/api_client.dart';
import '../../core/api/api_endpoints.dart';
import '../../core/api/api_exception.dart';
import '../../core/models/models.dart';

class MenusRepository {
  final ApiClient apiClient;

  MenusRepository(this.apiClient);

  Future<List<Menu>> getMenus({
    String? category,
    String? search,
    bool? available,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (category != null && category.isNotEmpty && category != 'all') {
        queryParams['category'] = category;
      }
      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }
      if (available != null) {
        queryParams['available'] = available ? 1 : 0;
      }

      final response = await apiClient.dio.get(
        ApiEndpoints.menus,
        queryParameters: queryParams,
        options: Options(headers: {'Accept': 'application/json'}),
      );

      final data = response.data;
      if (data is Map<String, dynamic> && data['menus'] != null) {
        return (data['menus'] as List).map((e) => Menu.fromJson(e)).toList();
      } else if (data is List) {
        return data.map((e) => Menu.fromJson(e)).toList();
      }
      return [];
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memuat daftar menu',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> toggleAvailability(int menuId, bool available) async {
    try {
      await apiClient.dio.patch(
        ApiEndpoints.menuDetail(menuId),
        data: {'available': available ? 1 : 0},
      );
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal mengubah ketersediaan menu',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> createMenu({
    required String name,
    required String description,
    required int price,
    required String category,
    required bool available,
  }) async {
    try {
      await apiClient.dio.post(
        ApiEndpoints.menus,
        data: {
          'name': name,
          'description': description,
          'price': price,
          'category': category,
          'available': available ? 1 : 0,
        },
      );
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal menambahkan menu baru',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> updateMenu({
    required int menuId,
    required String name,
    required String description,
    required int price,
    required String category,
    required bool available,
  }) async {
    try {
      await apiClient.dio.put(
        ApiEndpoints.menuDetail(menuId),
        data: {
          'name': name,
          'description': description,
          'price': price,
          'category': category,
          'available': available ? 1 : 0,
        },
      );
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memperbarui menu',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> deleteMenu(int menuId) async {
    try {
      await apiClient.dio.delete(ApiEndpoints.menuDetail(menuId));
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal menghapus menu',
        statusCode: e.response?.statusCode,
      );
    }
  }
}

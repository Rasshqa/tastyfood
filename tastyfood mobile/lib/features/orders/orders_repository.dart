import 'package:dio/dio.dart';
import '../../core/api/api_client.dart';
import '../../core/api/api_endpoints.dart';
import '../../core/api/api_exception.dart';
import '../../core/models/order.dart';

class OrdersRepository {
  final ApiClient apiClient;

  OrdersRepository(this.apiClient);

  Future<List<Order>> getOrders({
    String? status,
    String? paymentStatus,
    String? search,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (status != null) queryParams['status'] = status;
      if (paymentStatus != null) queryParams['payment_status'] = paymentStatus;
      if (search != null) queryParams['search'] = search;

      final response = await apiClient.dio.get(
        ApiEndpoints.orders,
        queryParameters: queryParams,
        options: Options(headers: {'Accept': 'application/json'}),
      );

      final data = response.data;
      if (data['success'] == true) {
        return (data['orders'] as List).map((e) => Order.fromJson(e)).toList();
      }
      throw ApiException('Gagal memuat pesanan');
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memuat pesanan',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<Order> getOrderDetail(int id) async {
    try {
      final response = await apiClient.dio.get(ApiEndpoints.orderDetail(id));
      final data = response.data;
      if (data is Map<String, dynamic> && data['order'] != null) {
        return Order.fromJson(data['order']);
      }
      return Order.fromJson(data);
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal memuat detail pesanan',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> updateOrderStatus(int id, String status) async {
    try {
      await apiClient.dio.patch(
        ApiEndpoints.updateOrderStatus(id),
        data: {'status': status},
      );
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal update status pesanan',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> approvePayment(int paymentId) async {
    try {
      await apiClient.dio.post(ApiEndpoints.approvePayment(paymentId));
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal menyetujui pembayaran',
        statusCode: e.response?.statusCode,
      );
    }
  }

  Future<void> rejectPayment(int paymentId, String reason) async {
    try {
      await apiClient.dio.post(
        ApiEndpoints.rejectPayment(paymentId),
        data: {'rejection_reason': reason},
      );
    } on DioException catch (e) {
      throw ApiException(
        e.response?.data['message'] ?? 'Gagal menolak pembayaran',
        statusCode: e.response?.statusCode,
      );
    }
  }
}

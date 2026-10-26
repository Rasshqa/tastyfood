import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../../core/models/order.dart';
import 'orders_repository.dart';

// Events
abstract class OrdersEvent extends Equatable {
  @override
  List<Object?> get props => [];
}

class OrdersLoadRequested extends OrdersEvent {
  final String? status;
  final String? paymentStatus;
  final String? search;

  OrdersLoadRequested({this.status, this.paymentStatus, this.search});

  @override
  List<Object?> get props => [status, paymentStatus, search];
}

class OrderStatusUpdateRequested extends OrdersEvent {
  final int orderId;
  final String status;

  OrderStatusUpdateRequested(this.orderId, this.status);

  @override
  List<Object?> get props => [orderId, status];
}

class PaymentApproveRequested extends OrdersEvent {
  final int paymentId;

  PaymentApproveRequested(this.paymentId);

  @override
  List<Object?> get props => [paymentId];
}

class PaymentRejectRequested extends OrdersEvent {
  final int paymentId;
  final String reason;

  PaymentRejectRequested(this.paymentId, this.reason);

  @override
  List<Object?> get props => [paymentId, reason];
}

// States
abstract class OrdersState extends Equatable {
  @override
  List<Object?> get props => [];
}

class OrdersInitial extends OrdersState {}

class OrdersLoading extends OrdersState {}

class OrdersLoaded extends OrdersState {
  final List<Order> orders;

  OrdersLoaded(this.orders);

  @override
  List<Object?> get props => [orders];
}

class OrdersError extends OrdersState {
  final String message;

  OrdersError(this.message);

  @override
  List<Object?> get props => [message];
}

class OrdersActionSuccess extends OrdersState {
  final String message;

  OrdersActionSuccess(this.message);

  @override
  List<Object?> get props => [message];
}

// Bloc
class OrdersBloc extends Bloc<OrdersEvent, OrdersState> {
  final OrdersRepository repository;

  OrdersBloc(this.repository) : super(OrdersInitial()) {
    on<OrdersLoadRequested>(_onLoadRequested);
    on<OrderStatusUpdateRequested>(_onStatusUpdateRequested);
    on<PaymentApproveRequested>(_onPaymentApproveRequested);
    on<PaymentRejectRequested>(_onPaymentRejectRequested);
  }

  Future<void> _onLoadRequested(
    OrdersLoadRequested event,
    Emitter<OrdersState> emit,
  ) async {
    emit(OrdersLoading());
    try {
      final orders = await repository.getOrders(
        status: event.status,
        paymentStatus: event.paymentStatus,
        search: event.search,
      );
      emit(OrdersLoaded(orders));
    } catch (e) {
      emit(OrdersError(e.toString()));
    }
  }

  Future<void> _onStatusUpdateRequested(
    OrderStatusUpdateRequested event,
    Emitter<OrdersState> emit,
  ) async {
    try {
      await repository.updateOrderStatus(event.orderId, event.status);
      emit(OrdersActionSuccess('Status berhasil diperbarui'));
      add(OrdersLoadRequested());
    } catch (e) {
      emit(OrdersError(e.toString()));
    }
  }

  Future<void> _onPaymentApproveRequested(
    PaymentApproveRequested event,
    Emitter<OrdersState> emit,
  ) async {
    try {
      await repository.approvePayment(event.paymentId);
      emit(OrdersActionSuccess('Pembayaran disetujui'));
      add(OrdersLoadRequested());
    } catch (e) {
      emit(OrdersError(e.toString()));
    }
  }

  Future<void> _onPaymentRejectRequested(
    PaymentRejectRequested event,
    Emitter<OrdersState> emit,
  ) async {
    try {
      await repository.rejectPayment(event.paymentId, event.reason);
      emit(OrdersActionSuccess('Pembayaran ditolak'));
      add(OrdersLoadRequested());
    } catch (e) {
      emit(OrdersError(e.toString()));
    }
  }
}

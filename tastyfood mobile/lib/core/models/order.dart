class Order {
  final int id;
  final String orderNumber;
  final String customerName;
  final String? customerEmail;
  final String customerPhone;
  final String deliveryMethod;
  final String? deliveryAddress;
  final String status;
  final int subtotal;
  final int deliveryFee;
  final int total;
  final String? notes;
  final DateTime createdAt;
  final Payment? payment;
  final List<OrderItem>? items;

  Order({
    required this.id,
    required this.orderNumber,
    required this.customerName,
    this.customerEmail,
    required this.customerPhone,
    required this.deliveryMethod,
    this.deliveryAddress,
    required this.status,
    required this.subtotal,
    required this.deliveryFee,
    required this.total,
    this.notes,
    required this.createdAt,
    this.payment,
    this.items,
  });

  factory Order.fromJson(Map<String, dynamic> json) => Order(
        id: json['id'],
        orderNumber: json['order_number'],
        customerName: json['customer_name'],
        customerEmail: json['customer_email'],
        customerPhone: json['customer_phone'],
        deliveryMethod: json['delivery_method'],
        deliveryAddress: json['delivery_address'],
        status: json['status'],
        subtotal: json['subtotal'],
        deliveryFee: json['delivery_fee'],
        total: json['total'],
        notes: json['notes'],
        createdAt: DateTime.parse(json['created_at']),
        payment: json['payment'] != null ? Payment.fromJson(json['payment']) : null,
        items: json['items'] != null
            ? (json['items'] as List).map((e) => OrderItem.fromJson(e)).toList()
            : null,
      );
}

class OrderItem {
  final int id;
  final int menuId;
  final String menuName;
  final int menuPrice;
  final int quantity;
  final int subtotal;

  OrderItem({
    required this.id,
    required this.menuId,
    required this.menuName,
    required this.menuPrice,
    required this.quantity,
    required this.subtotal,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) => OrderItem(
        id: json['id'],
        menuId: json['menu_id'],
        menuName: json['menu_name'],
        menuPrice: json['menu_price'],
        quantity: json['quantity'],
        subtotal: json['subtotal'],
      );
}

class Payment {
  final int id;
  final int amount;
  final String status;
  final String? proofImage;
  final String? rejectionReason;
  final DateTime? verifiedAt;
  final PaymentMethod? paymentMethod;

  Payment({
    required this.id,
    required this.amount,
    required this.status,
    this.proofImage,
    this.rejectionReason,
    this.verifiedAt,
    this.paymentMethod,
  });

  factory Payment.fromJson(Map<String, dynamic> json) => Payment(
        id: json['id'],
        amount: json['amount'],
        status: json['status'],
        proofImage: json['proof_image'],
        rejectionReason: json['rejection_reason'],
        verifiedAt: json['verified_at'] != null ? DateTime.parse(json['verified_at']) : null,
        paymentMethod: json['payment_method'] != null
            ? PaymentMethod.fromJson(json['payment_method'])
            : null,
      );
}

class PaymentMethod {
  final int id;
  final String name;
  final String type;
  final String? accountName;
  final String? accountNumber;

  PaymentMethod({
    required this.id,
    required this.name,
    required this.type,
    this.accountName,
    this.accountNumber,
  });

  factory PaymentMethod.fromJson(Map<String, dynamic> json) => PaymentMethod(
        id: json['id'],
        name: json['name'],
        type: json['type'],
        accountName: json['account_name'],
        accountNumber: json['account_number'],
      );
}

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
        id: json['id'] ?? 0,
        orderNumber: json['order_number'] ?? '',
        customerName: json['customer_name'] ?? '',
        customerEmail: json['customer_email'],
        customerPhone: json['customer_phone'] ?? '',
        deliveryMethod: json['delivery_method'] ?? 'pickup',
        deliveryAddress: json['delivery_address'],
        status: json['status'] ?? 'pending',
        subtotal: json['subtotal'] ?? json['total'] ?? 0,
        deliveryFee: json['delivery_fee'] ?? 0,
        total: json['total'] ?? 0,
        notes: json['notes'],
        createdAt: json['created_at'] != null
            ? DateTime.tryParse(json['created_at'].toString()) ?? DateTime.now()
            : DateTime.now(),
        payment: json['payment'] != null
            ? (json['payment'] is Map<String, dynamic>
                ? Payment.fromJson(json['payment'])
                : Payment(
                    id: 0,
                    amount: json['total'] ?? 0,
                    status: json['payment_status']?.toString() ?? 'unpaid',
                  ))
            : (json['payment_status'] != null
                ? Payment(
                    id: 0,
                    amount: json['total'] ?? 0,
                    status: json['payment_status'].toString(),
                  )
                : null),
        items: json['items'] != null
            ? (json['items'] as List)
                .whereType<Map<String, dynamic>>()
                .map((e) => OrderItem.fromJson(e))
                .toList()
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
        id: json['id'] ?? 0,
        menuId: json['menu_id'] ?? 0,
        menuName: json['menu_name'] ?? '',
        menuPrice: json['menu_price'] ?? 0,
        quantity: json['quantity'] ?? 1,
        subtotal: json['subtotal'] ?? 0,
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
        id: json['id'] ?? 0,
        amount: json['amount'] ?? 0,
        status: json['status'] ?? 'unpaid',
        proofImage: json['proof_image'],
        rejectionReason: json['rejection_reason'],
        verifiedAt: json['verified_at'] != null
            ? DateTime.tryParse(json['verified_at'].toString())
            : null,
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
        id: json['id'] ?? 0,
        name: json['name'] ?? '',
        type: json['type'] ?? '',
        accountName: json['account_name'],
        accountNumber: json['account_number'],
      );
}

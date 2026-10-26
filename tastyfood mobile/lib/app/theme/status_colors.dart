import 'package:flutter/material.dart';

/// Order and payment status color mappings.
///
/// Each status has a background color and text color to ensure WCAG AA contrast.
/// Always display a text label alongside color to meet accessibility standards.
class StatusColors {
  StatusColors._();

  // ---------------------------------------------------------------------------
  // Order status mapping
  // ---------------------------------------------------------------------------

  static const Map<String, StatusColorPair> orderStatus = {
    'pending': StatusColorPair(
      background: Color(0xFFFEF3C7),
      text: Color(0xFF92400E),
      label: 'Menunggu',
    ),
    'confirmed': StatusColorPair(
      background: Color(0xFFDBEAFE),
      text: Color(0xFF1E40AF),
      label: 'Dikonfirmasi',
    ),
    'preparing': StatusColorPair(
      background: Color(0xFFFED7AA),
      text: Color(0xFF9A3412),
      label: 'Sedang dimasak',
    ),
    'ready': StatusColorPair(
      background: Color(0xFFD1FAE5),
      text: Color(0xFF065F46),
      label: 'Siap',
    ),
    'in_transit': StatusColorPair(
      background: Color(0xFFE9D5FF),
      text: Color(0xFF6B21A8),
      label: 'Dalam perjalanan',
    ),
    'delivered': StatusColorPair(
      background: Color(0xFFD1FAE5),
      text: Color(0xFF065F46),
      label: 'Terkirim',
    ),
    'completed': StatusColorPair(
      background: Color(0xFFE5E7EB),
      text: Color(0xFF374151),
      label: 'Selesai',
    ),
    'cancelled': StatusColorPair(
      background: Color(0xFFFEE2E2),
      text: Color(0xFF991B1B),
      label: 'Dibatalkan',
    ),
  };

  // ---------------------------------------------------------------------------
  // Payment status mapping
  // ---------------------------------------------------------------------------

  static const Map<String, StatusColorPair> paymentStatus = {
    'unpaid': StatusColorPair(
      background: Color(0xFFE5E7EB),
      text: Color(0xFF6B7280),
      label: 'Belum dibayar',
    ),
    'waiting_verification': StatusColorPair(
      background: Color(0xFFFEF3C7),
      text: Color(0xFF92400E),
      label: 'Menunggu verifikasi',
    ),
    'paid': StatusColorPair(
      background: Color(0xFFD1FAE5),
      text: Color(0xFF065F46),
      label: 'Dibayar',
    ),
    'rejected': StatusColorPair(
      background: Color(0xFFFEE2E2),
      text: Color(0xFF991B1B),
      label: 'Ditolak',
    ),
    'expired': StatusColorPair(
      background: Color(0xFFE5E7EB),
      text: Color(0xFF374151),
      label: 'Kedaluwarsa',
    ),
  };

  /// Get order status colors or fallback to neutral.
  static StatusColorPair getOrderStatus(String status) {
    return orderStatus[status] ??
        const StatusColorPair(
          background: Color(0xFFE5E7EB),
          text: Color(0xFF374151),
          label: 'Unknown',
        );
  }

  /// Get payment status colors or fallback to neutral.
  static StatusColorPair getPaymentStatus(String status) {
    return paymentStatus[status] ??
        const StatusColorPair(
          background: Color(0xFFE5E7EB),
          text: Color(0xFF6B7280),
          label: 'Unknown',
        );
  }
}

/// Holds background and text colors for a status.
class StatusColorPair {
  final Color background;
  final Color text;
  final String label;

  const StatusColorPair({
    required this.background,
    required this.text,
    required this.label,
  });
}

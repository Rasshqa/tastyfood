import 'package:flutter/material.dart';

/// Spacing design tokens — all values are multiples of 4.
class AppSpacing {
  AppSpacing._();

  static const double xs = 4;
  static const double sm = 8;
  static const double md = 16;
  static const double lg = 24;
  static const double xl = 32;
  static const double xxl = 48;
}

/// Border radius tokens.
class AppRadius {
  AppRadius._();

  static const double small = 10;
  static const double medium = 16;
  static const double large = 22;
  static const double pill = 999;
}

/// Shadow values for glass cards and elevated surfaces.
class AppShadows {
  AppShadows._();

  /// Soft shadow for glass cards.
  static const List<BoxShadow> glass = [
    BoxShadow(
      color: Color(0x0D111827),
      blurRadius: 22,
      offset: Offset(0, 8),
    ),
  ];

  /// Subtle elevation for cards.
  static const List<BoxShadow> card = [
    BoxShadow(
      color: Color(0x0A111827),
      blurRadius: 12,
      offset: Offset(0, 4),
    ),
  ];
}

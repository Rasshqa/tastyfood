import 'package:flutter/material.dart';

/// Central design tokens for the Padang Rice Admin Mobile app.
///
/// All colors defined here come straight from the DESIGN.md specification.
/// Never hard-code colors in widgets — always reference these tokens.
class AppColors {
  AppColors._();

  // ---------------------------------------------------------------------------
  // Brand / core palette
  // ---------------------------------------------------------------------------

  /// Primary actions, active navigation, strong text.
  static const Color primary = Color(0xFF1F2937);

  /// Strong headings and pressed states.
  static const Color primaryDark = Color(0xFF111827);

  /// Brand accent, selection indicator.
  static const Color accent = Color(0xFFEAB308);

  /// Brand warm orange accent for food & cooking statuses.
  static const Color accentOrange = Color(0xFFF97316);

  /// Soft accent background.
  static const Color accentLight = Color(0xFFFEF3C7);

  // ---------------------------------------------------------------------------
  // Semantic status colors
  // ---------------------------------------------------------------------------

  static const Color success = Color(0xFF10B981);
  static const Color warning = Color(0xFFF59E0B);
  static const Color error = Color(0xFFEF4444);
  static const Color info = Color(0xFF3B82F6);

  // ---------------------------------------------------------------------------
  // Surfaces
  // ---------------------------------------------------------------------------

  /// Application base background.
  static const Color background = Color(0xFFF3F4F6);

  /// Primary content and form surface.
  static const Color surface = Color(0xFFFFFFFF);

  /// White tint used for glass cards.
  static const Color glassTint = Color(0xFFFFFFFF);

  /// Modal overlay.
  static const Color overlay = Color(0xFF111827);

  // ---------------------------------------------------------------------------
  // Text
  // ---------------------------------------------------------------------------

  static const Color textPrimary = Color(0xFF111827);
  static const Color textSecondary = Color(0xFF6B7280);
  static const Color textDisabled = Color(0xFF9CA3AF);
  static const Color textOnPrimary = Color(0xFFFFFFFF);

  // ---------------------------------------------------------------------------
  // Borders & dividers
  // ---------------------------------------------------------------------------

  static const Color border = Color(0xFFE5E7EB);

  // ---------------------------------------------------------------------------
  // Opacity helpers used by glass surfaces
  // ---------------------------------------------------------------------------

  static Color glassFill(Color tint, {double opacity = 0.78}) =>
      tint.withValues(alpha: opacity);

  static Color glassBorder(Color tint, {double opacity = 0.72}) =>
      tint.withValues(alpha: opacity);

  static Color shadow({double opacity = 0.05}) =>
      primaryDark.withValues(alpha: opacity);
}

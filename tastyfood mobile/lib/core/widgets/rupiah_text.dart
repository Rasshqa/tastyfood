import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

/// Formats and displays Indonesian Rupiah currency.
class RupiahText extends StatelessWidget {
  final double amount;
  final TextStyle? style;
  final bool compact;

  const RupiahText({
    super.key,
    required this.amount,
    this.style,
    this.compact = false,
  });

  static String format(double amount, {bool compact = false}) {
    final formatter = NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: compact ? 0 : 0,
    );
    return formatter.format(amount);
  }

  @override
  Widget build(BuildContext context) {
    return Text(
      format(amount, compact: compact),
      style: style,
    );
  }
}

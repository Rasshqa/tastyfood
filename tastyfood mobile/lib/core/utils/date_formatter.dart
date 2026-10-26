import 'package:intl/intl.dart';

/// Date and time formatting utilities.
class DateFormatter {
  DateFormatter._();

  static final _dateFormat = DateFormat('d MMM yyyy', 'id_ID');
  static final _dateTimeFormat = DateFormat('d MMM yyyy, HH:mm', 'id_ID');
  static final _timeFormat = DateFormat('HH:mm', 'id_ID');
  static final _shortDateFormat = DateFormat('d MMM', 'id_ID');

  /// Format: "5 Jan 2025"
  static String formatDate(DateTime date) => _dateFormat.format(date);

  /// Format: "5 Jan 2025, 14:30"
  static String formatDateTime(DateTime date) => _dateTimeFormat.format(date);

  /// Format: "14:30"
  static String formatTime(DateTime time) => _timeFormat.format(time);

  /// Format: "5 Jan"
  static String formatShortDate(DateTime date) => _shortDateFormat.format(date);

  /// Returns a human-readable relative time.
  static String timeAgo(DateTime dateTime) {
    final now = DateTime.now();
    final diff = now.difference(dateTime);

    if (diff.inSeconds < 60) return 'Baru saja';
    if (diff.inMinutes < 60) return '${diff.inMinutes}m';
    if (diff.inHours < 24) return '${diff.inHours}j';
    if (diff.inDays < 7) return '${diff.inDays}h';
    return formatShortDate(dateTime);
  }
}

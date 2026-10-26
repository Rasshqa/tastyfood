# Padang Rice Admin Mobile - Design System

Complete Flutter design system implementation for the Padang Rice Admin mobile application.

## 📐 Design System Overview

This implementation follows the specifications in `design(1).md` with a complete glassmorphism design system, reusable components, and consistent visual language.

### Design Principles

1. **Soft glassmorphism** — translucent cards with measured blur, never overdone
2. **Content over decoration** — no important text on busy backgrounds
3. **Neutral backgrounds** — off-white and warm gray with golden accent
4. **Conventional status colors** — green/red/yellow/blue following standards
5. **Consistency** — unified radius, spacing, typography, and behavior

## 🎨 Design Tokens

### Colors (`lib/app/theme/app_colors.dart`)

```dart
AppColors.primary        // #1F2937 - Primary actions, active nav
AppColors.accent         // #EAB308 - Brand accent, golden yellow
AppColors.success        // #10B981 - Success states
AppColors.warning        // #F59E0B - Warnings
AppColors.error          // #EF4444 - Errors
AppColors.background     // #F3F4F6 - App background
AppColors.surface        // #FFFFFF - Card surfaces
```

### Status Colors (`lib/app/theme/status_colors.dart`)

Order and payment status mappings with background + text colors:

```dart
StatusColors.getOrderStatus('pending')      // Returns StatusColorPair
StatusColors.getPaymentStatus('paid')       // Returns StatusColorPair
```

**Order statuses:** pending, confirmed, preparing, ready, in_transit, delivered, completed, cancelled

**Payment statuses:** unpaid, waiting_verification, paid, rejected, expired

### Typography (`lib/app/theme/app_typography.dart`)

Using Google Fonts Inter with predefined styles:

- `AppTypography.display` — 30px/700
- `AppTypography.h1` — 26px/700
- `AppTypography.h2` — 22px/600
- `AppTypography.h3` — 18px/600
- `AppTypography.body` — 15px/400
- `AppTypography.bodyMedium` — 15px/500
- `AppTypography.label` — 13px/600
- `AppTypography.caption` — 12px/400
- `AppTypography.button` — 14px/600

### Spacing & Layout (`lib/app/theme/app_spacing.dart`)

```dart
AppSpacing.xs    // 4
AppSpacing.sm    // 8
AppSpacing.md    // 16
AppSpacing.lg    // 24
AppSpacing.xl    // 32
AppSpacing.xxl   // 48

AppRadius.small   // 10
AppRadius.medium  // 16
AppRadius.large   // 22
AppRadius.pill    // 999
```

## 🧩 Core Components

### Glass Components

**GlassCard** — Glassmorphism card with blur and translucent fill
```dart
GlassCard(
  borderRadius: AppRadius.large,
  blurSigma: 14,
  fillOpacity: 0.78,
  child: YourContent(),
)
```

### Buttons

**PrimaryButton** — Main action button with loading state
```dart
PrimaryButton(
  label: 'Simpan',
  onPressed: () {},
  isLoading: false,
  icon: Icons.save,
)
```

**SecondaryButton** — Outline button
**DestructiveButton** — Red button for dangerous actions

### Forms

**AppTextField** — Standard text input with label above
```dart
AppTextField(
  label: 'Email',
  hint: 'email@example.com',
  controller: emailController,
  errorText: 'Email tidak valid',
)
```

**SearchField** — Search input with icon and clear button

**FilterChipGroup** — Horizontal scrolling filter chips

### Status & States

**StatusBadge** — Color-coded status indicator
```dart
StatusBadge(
  status: 'confirmed',
  type: StatusType.order,
  compact: true,
)
```

**EmptyState** — Empty screen with icon, title, description, action
**ErrorState** — Error screen with retry button
**OfflineBanner** — Offline indicator banner
**LoadingSkeleton** — Shimmer loading placeholders

### Cards

**OrderCard** — Order list item with status badges
**PaymentQueueCard** — Payment verification queue item
**MenuCard** — Menu item with availability toggle
**MetricCard** — Dashboard metric display

### Chat Components

**Avatar** — User avatar with initials fallback
**ConversationTile** — Chat inbox item with unread badge
**ChatBubble** — Message bubble (admin/customer/system)

### Utilities

**ConfirmActionDialog** — Confirmation dialog for critical actions
```dart
final confirmed = await ConfirmActionDialog.show(
  context,
  title: 'Konfirmasi',
  message: 'Apakah Anda yakin?',
  isDestructive: true,
);
```

**ImagePreview** — Fullscreen image viewer with zoom
**RupiahText** — Indonesian currency formatter
**DateFormatter** — Date/time formatting utilities

## 🏗️ Project Structure

```
lib/
├── app/
│   ├── theme/
│   │   ├── app_colors.dart          # Color tokens
│   │   ├── status_colors.dart       # Status mappings
│   │   ├── app_typography.dart      # Typography tokens
│   │   ├── app_spacing.dart         # Spacing/radius/shadows
│   │   └── app_theme.dart           # ThemeData config
│   ├── app.dart                      # Root widget
│   └── router.dart                   # Navigation config
├── core/
│   ├── widgets/
│   │   ├── glass_card.dart
│   │   ├── status_badge.dart
│   │   ├── app_button.dart
│   │   ├── app_text_field.dart
│   │   ├── app_scaffold.dart
│   │   ├── state_widgets.dart
│   │   ├── loading_skeleton.dart
│   │   ├── metric_card.dart
│   │   ├── order_cards.dart
│   │   ├── menu_card.dart
│   │   ├── conversation_tile.dart
│   │   ├── chat_bubble.dart
│   │   ├── confirm_action_dialog.dart
│   │   ├── image_preview.dart
│   │   ├── rupiah_text.dart
│   │   ├── filter_chip_group.dart
│   │   └── widgets.dart             # Barrel export
│   └── utils/
│       └── date_formatter.dart
├── features/                          # Feature modules (auth, dashboard, etc.)
└── main.dart
```

## 🚀 Usage Examples

### Creating a Dashboard Card

```dart
GlassCard(
  child: Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      Text('Pendapatan Hari Ini', style: AppTypography.label),
      const SizedBox(height: AppSpacing.sm),
      RupiahText(
        amount: 2500000,
        style: AppTypography.h1.copyWith(color: AppColors.success),
      ),
    ],
  ),
)
```

### Creating an Order List

```dart
ListView.builder(
  itemCount: orders.length,
  itemBuilder: (context, index) {
    final order = orders[index];
    return OrderCard(
      orderNumber: order.number,
      customerName: order.customer,
      phoneNumber: order.phone,
      orderTime: DateFormatter.formatDateTime(order.createdAt),
      total: RupiahText.format(order.total),
      status: order.status,
      paymentStatus: order.paymentStatus,
      onTap: () => context.push('/orders/${order.id}'),
    );
  },
)
```

### Using Bottom Navigation

```dart
AppScaffold(
  title: 'Beranda',
  showBottomNav: true,
  currentIndex: 0,
  onNavTap: (index) => setState(() => _currentIndex = index),
  body: YourContent(),
)
```

## 🎯 Design Guidelines

### When to Use Glass vs Solid

**Use glass cards for:**
- Dashboard summaries
- Quick actions
- Filter panels
- Navigation elements

**Use solid white cards for:**
- Long forms
- Dense lists
- Order details with lots of text
- Payment proofs requiring maximum contrast

### Accessibility

- ✅ 44×44px minimum touch targets
- ✅ WCAG AA contrast ratios
- ✅ Semantic labels on icons
- ✅ Status conveyed through text + color
- ✅ Dynamic text size support

### Performance

- ⚡ Avoid multiple BackdropFilter on same screen (GPU expensive)
- ⚡ Use ListView.builder for long lists
- ⚡ Cache images with cached_network_image
- ⚡ Skeleton loaders preserve layout during load

## 📦 Dependencies

```yaml
flutter_bloc: ^8.1.6          # State management
dio: ^5.7.0                   # HTTP client
flutter_secure_storage: ^9.2.2 # Token storage
go_router: ^14.6.1            # Navigation
intl: ^0.19.0                 # Formatting
cached_network_image: ^3.4.1  # Image caching
google_fonts: ^6.2.1          # Inter font
shimmer: ^3.0.0               # Loading skeletons
google_maps_flutter: ^2.9.0   # Maps
firebase_messaging: ^15.1.3   # Push notifications
```

## 🏃 Getting Started

```bash
# Install dependencies
flutter pub get

# Run the app
flutter run

# Build APK
flutter build apk --release

# Build iOS
flutter build ios --release
```

## 📝 Next Steps

The design system foundation is complete. Next implementation phases:

1. **API Layer** — Dio client, endpoints, auth interceptor
2. **Feature Screens** — Login, Dashboard, Orders, Payments, Chat
3. **State Management** — BLoC implementation for each feature
4. **Firebase Setup** — Push notifications configuration
5. **Maps Integration** — Google Maps for delivery tracking
6. **Testing** — Unit tests for BLoCs, widget tests for components

## 📖 References

- Design specification: `design(1).md`
- All colors, spacing, and typography follow the specification exactly
- Glass effect parameters tuned to balance aesthetics and performance
- Status colors ensure WCAG AA contrast compliance

---

**Built with Flutter** • Design by DESIGN.md specification • Implementation follows ponytail principles

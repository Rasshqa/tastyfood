import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:go_router/go_router.dart';
import '../core/api/api_client.dart';
import '../core/api/api_endpoints.dart';
import '../features/auth/auth_bloc.dart';
import '../features/auth/auth_repository.dart';
import '../features/auth/login_screen.dart';
import '../features/chat/chat_bloc.dart';
import '../features/chat/chat_repository.dart';
import '../features/chat/chat_screen.dart';
import '../features/chat/chat_detail_screen.dart';
import '../features/dashboard/dashboard_bloc.dart';
import '../features/dashboard/dashboard_repository.dart';
import '../features/dashboard/dashboard_screen.dart';
import '../features/menus/menus_bloc.dart';
import '../features/menus/menus_repository.dart';
import '../features/menus/menus_screen.dart';
import '../features/messages/messages_repository.dart';
import '../features/messages/messages_screen.dart';
import '../features/more/more_screen.dart';
import '../features/orders/order_detail_screen.dart';
import '../features/orders/orders_bloc.dart';
import '../features/orders/orders_repository.dart';
import '../features/orders/orders_screen.dart';
import '../features/payments/payment_queue_screen.dart';
import '../features/settings/settings_repository.dart';
import '../features/settings/settings_screen.dart';
import 'theme/app_theme.dart';

class App extends StatelessWidget {
  const App({super.key});

  @override
  Widget build(BuildContext context) {
    final dio = Dio(BaseOptions(baseUrl: ApiEndpoints.baseUrl));
    final storage = const FlutterSecureStorage();
    final apiClient = ApiClient(dio: dio, storage: storage);

    return MultiRepositoryProvider(
      providers: [
        RepositoryProvider(create: (_) => AuthRepository(apiClient)),
        RepositoryProvider(create: (_) => OrdersRepository(apiClient)),
        RepositoryProvider(create: (_) => ChatRepository(apiClient)),
        RepositoryProvider(create: (_) => DashboardRepository(apiClient)),
        RepositoryProvider(create: (_) => MenusRepository(apiClient)),
        RepositoryProvider(create: (_) => SettingsRepository(apiClient)),
        RepositoryProvider(create: (_) => MessagesRepository(apiClient)),
      ],
      child: MultiBlocProvider(
        providers: [
          BlocProvider(
            create: (context) => AuthBloc(context.read<AuthRepository>())
              ..add(AuthCheckRequested()),
          ),
          BlocProvider(
            create: (context) => OrdersBloc(context.read<OrdersRepository>()),
          ),
          BlocProvider(
            create: (context) => ChatBloc(context.read<ChatRepository>()),
          ),
          BlocProvider(
            create: (context) => DashboardBloc(context.read<DashboardRepository>()),
          ),
          BlocProvider(
            create: (context) => MenusBloc(context.read<MenusRepository>()),
          ),
        ],
        child: MaterialApp.router(
          title: 'Padang Rice Admin',
          debugShowCheckedModeBanner: false,
          theme: AppTheme.lightTheme,
          routerConfig: _router,
        ),
      ),
    );
  }
}

final _router = GoRouter(
  initialLocation: '/login',
  redirect: (context, state) {
    final authState = context.read<AuthBloc>().state;
    final isLoginRoute = state.matchedLocation == '/login';

    if (authState is AuthAuthenticated && isLoginRoute) {
      return '/dashboard';
    }
    if (authState is AuthUnauthenticated && !isLoginRoute) {
      return '/login';
    }
    return null;
  },
  routes: [
    GoRoute(
      path: '/login',
      builder: (context, state) => const LoginScreen(),
    ),
    GoRoute(
      path: '/dashboard',
      builder: (context, state) => const DashboardScreen(),
    ),
    GoRoute(
      path: '/orders',
      builder: (context, state) => const OrdersScreen(),
    ),
    GoRoute(
      path: '/orders/:id',
      builder: (context, state) {
        final id = int.tryParse(state.pathParameters['id'] ?? '') ?? 0;
        return OrderDetailScreen(orderId: id);
      },
    ),
    GoRoute(
      path: '/payments',
      builder: (context, state) => const PaymentQueueScreen(),
    ),
    GoRoute(
      path: '/menus',
      builder: (context, state) => const MenusScreen(),
    ),
    GoRoute(
      path: '/messages',
      builder: (context, state) => const MessagesScreen(),
    ),
    GoRoute(
      path: '/chat',
      builder: (context, state) => const ChatScreen(),
    ),
    GoRoute(
      path: '/chat/:id',
      builder: (context, state) {
        final id = int.parse(state.pathParameters['id']!);
        return ChatDetailScreen(conversationId: id);
      },
    ),
    GoRoute(
      path: '/settings',
      builder: (context, state) => const SettingsScreen(),
    ),
    GoRoute(
      path: '/more',
      builder: (context, state) => const MoreScreen(),
    ),
  ],
);

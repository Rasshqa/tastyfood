import 'dart:async';
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

class GoRouterRefreshStream extends ChangeNotifier {
  late final StreamSubscription<dynamic> _subscription;

  GoRouterRefreshStream(Stream<dynamic> stream) {
    notifyListeners();
    _subscription = stream.asBroadcastStream().listen((_) => notifyListeners());
  }

  @override
  void dispose() {
    _subscription.cancel();
    super.dispose();
  }
}

class App extends StatefulWidget {
  const App({super.key});

  @override
  State<App> createState() => _AppState();
}

class _AppState extends State<App> {
  late final Dio _dio;
  late final FlutterSecureStorage _storage;
  late final ApiClient _apiClient;
  late final AuthRepository _authRepository;
  late final OrdersRepository _ordersRepository;
  late final ChatRepository _chatRepository;
  late final DashboardRepository _dashboardRepository;
  late final MenusRepository _menusRepository;
  late final SettingsRepository _settingsRepository;
  late final MessagesRepository _messagesRepository;
  late final AuthBloc _authBloc;
  late final GoRouter _router;

  @override
  void initState() {
    super.initState();
    _dio = Dio(BaseOptions(baseUrl: ApiEndpoints.baseUrl));
    _storage = const FlutterSecureStorage();
    _apiClient = ApiClient(dio: _dio, storage: _storage);
    _authRepository = AuthRepository(_apiClient);
    _ordersRepository = OrdersRepository(_apiClient);
    _chatRepository = ChatRepository(_apiClient);
    _dashboardRepository = DashboardRepository(_apiClient);
    _menusRepository = MenusRepository(_apiClient);
    _settingsRepository = SettingsRepository(_apiClient);
    _messagesRepository = MessagesRepository(_apiClient);

    _authBloc = AuthBloc(_authRepository)..add(AuthCheckRequested());

    _apiClient.onUnauthorized = () {
      _authBloc.add(AuthLogoutRequested());
    };

    _router = GoRouter(
      initialLocation: '/login',
      refreshListenable: GoRouterRefreshStream(_authBloc.stream),
      redirect: (context, state) {
        final authState = _authBloc.state;
        final isLoginRoute = state.matchedLocation == '/login';

        if (authState is AuthAuthenticated && isLoginRoute) {
          return '/dashboard';
        }
        if (authState is! AuthAuthenticated && !isLoginRoute) {
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
  }

  @override
  void dispose() {
    _authBloc.close();
    _router.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return MultiRepositoryProvider(
      providers: [
        RepositoryProvider.value(value: _authRepository),
        RepositoryProvider.value(value: _ordersRepository),
        RepositoryProvider.value(value: _chatRepository),
        RepositoryProvider.value(value: _dashboardRepository),
        RepositoryProvider.value(value: _menusRepository),
        RepositoryProvider.value(value: _settingsRepository),
        RepositoryProvider.value(value: _messagesRepository),
      ],
      child: MultiBlocProvider(
        providers: [
          BlocProvider.value(value: _authBloc),
          BlocProvider(
            create: (context) => OrdersBloc(_ordersRepository),
          ),
          BlocProvider(
            create: (context) => ChatBloc(_chatRepository),
          ),
          BlocProvider(
            create: (context) => DashboardBloc(_dashboardRepository),
          ),
          BlocProvider(
            create: (context) => MenusBloc(_menusRepository),
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

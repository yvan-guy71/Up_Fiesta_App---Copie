import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:firebase_core/firebase_core.dart';
import 'firebase_options.dart';
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';
import 'screens/onboarding_screen.dart';
import 'screens/provider_dashboard_screen.dart';
import 'services/auth_service.dart';
import 'services/notification_service.dart';

// Notifier global pour le mode sombre
final ValueNotifier<ThemeMode> themeNotifier = ValueNotifier(ThemeMode.light);
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialisation de la locale et de l'Auth
  await initializeDateFormatting('fr_FR', null);
  final authService = AuthService();
  await authService.init();

  // Lancement immédiat de l'UI pour éviter de bloquer au splash
  runApp(const MyApp());

  // Initialisation asynchrone des services tiers (Firebase, etc.)
  _initServices();
}

Future<void> _initServices() async {
  try {
    await Firebase.initializeApp(
      options: DefaultFirebaseOptions.currentPlatform,
    );
    await NotificationService().init();
  } catch (e) {
    debugPrint("Erreur d'initialisation des services (Firebase/Notifications) : $e");
  }
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    final authService = AuthService();

    return ValueListenableBuilder<ThemeMode>(
      valueListenable: themeNotifier,
      builder: (_, ThemeMode currentMode, __) {
        return MaterialApp(
          title: 'Upfiesta',
          debugShowCheckedModeBanner: false,
          themeMode: currentMode,
          navigatorKey: navigatorKey,
          routes: {
            '/home': (context) => const HomeScreen(),
            '/login': (context) => const LoginScreen(),
          },
          theme: ThemeData(
            primaryColor: const Color(0xFF001489),
            scaffoldBackgroundColor: Colors.white,
            colorScheme: ColorScheme.fromSeed(
              seedColor: const Color(0xFF001489),
              primary: const Color(0xFF001489),
              secondary: const Color(0xFFFF6D00),
            ),
            useMaterial3: true,
            textTheme: GoogleFonts.poppinsTextTheme(),
          ),
          darkTheme: ThemeData(
            brightness: Brightness.dark,
            primaryColor: const Color(0xFF001489),
            scaffoldBackgroundColor: const Color(0xFF121212),
            colorScheme: ColorScheme.fromSeed(
              brightness: Brightness.dark,
              seedColor: const Color(0xFF001489),
              primary: const Color(0xFF001489),
              secondary: const Color(0xFFFF6D00),
              surface: const Color(0xFF1E1E1E),
            ),
            useMaterial3: true,
            textTheme: GoogleFonts.poppinsTextTheme(ThemeData.dark().textTheme),
          ),
          // Dispatcher de route selon l'authentification et le rôle
          home: ListenableBuilder(
            listenable: authService,
            builder: (context, _) {
              if (!authService.isAuthenticated) {
                return const OnboardingScreen();
              }

              // Redirection selon le rôle
              if (authService.userRole == 'provider' || authService.userRole == 'admin') {
                return const ProviderDashboardScreen();
              }

              return const HomeScreen();
            },
          ),
        );
      },
    );
  }
}




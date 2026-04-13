import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import '../main.dart';
import '../screens/bookings_screen.dart';
import '../screens/chat_screen.dart';
import 'api_service.dart';
import 'auth_service.dart';

class NotificationService {
  static final NotificationService _instance = NotificationService._internal();
  factory NotificationService() => _instance;
  NotificationService._internal();

  final FirebaseMessaging _fcm = FirebaseMessaging.instance;
  final FlutterLocalNotificationsPlugin _localNotifications = FlutterLocalNotificationsPlugin();
  final ApiService _apiService = ApiService();

  Future<void> init() async {
    // 1. Demander la permission
    await _fcm.requestPermission(alert: true, badge: true, sound: true);

    // 2. Configurer les notifications locales
    const AndroidInitializationSettings initializationSettingsAndroid =
        AndroidInitializationSettings('@mipmap/ic_launcher');

    const InitializationSettings initializationSettings = InitializationSettings(
      android: initializationSettingsAndroid,
    );

    await _localNotifications.initialize(
      initializationSettings,
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        _handleMessageClick(response.payload);
      },
    );

    // 3. Écouter les messages en premier plan
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      _showLocalNotification(message);
    });

    // 4. Écouter le clic quand l'app est en arrière-plan ou fermée
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      _handleMessageClick(message.data['type'], data: message.data);
    });

    // 5. Envoyer le token au serveur
    String? token = await _fcm.getToken();
    if (token != null && AuthService().isAuthenticated) {
      await _apiService.updateFcmToken(token);
    }
  }

  Future<void> _showLocalNotification(RemoteMessage message) async {
    const AndroidNotificationDetails androidDetails = AndroidNotificationDetails(
      'up_fiesta_channel',
      'Upfiesta Notifications',
      importance: Importance.max,
      priority: Priority.high,
    );

    await _localNotifications.show(
      message.hashCode,
      message.notification?.title,
      message.notification?.body,
      const NotificationDetails(android: androidDetails),
      payload: message.data['type'],
    );
  }

  void _handleMessageClick(String? type, {Map<String, dynamic>? data}) {
    if (navigatorKey.currentState == null) return;

    if (type == 'chat_message' && data != null) {
      final recipientId = int.tryParse(data['sender_id']?.toString() ?? '') ?? 0;
      if (recipientId > 0) {
        navigatorKey.currentState?.push(
          MaterialPageRoute(
            builder: (_) => ChatScreen(
              otherUser: {
                'id': recipientId,
                'name': data['sender_name'] ?? 'Utilisateur',
              },
            ),
          ),
        );
      }
    } else if (type == 'new_booking' || type == 'booking_status') {
      navigatorKey.currentState?.push(
        MaterialPageRoute(builder: (_) => const BookingsScreen()),
      );
    }
  }
}




import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';

class AuthService extends ChangeNotifier {
  static const String baseUrl = 'https://upfiesta.com/api';

  static final AuthService _instance = AuthService._internal();
  factory AuthService() => _instance;
  AuthService._internal();

  String? _token;
  Map<String, dynamic>? _user;

  final GoogleSignIn _googleSignIn = GoogleSignIn(
    clientId: kIsWeb ? '900424187779-our7t61tnt5kbv6th5vva4up8mo8028v.apps.googleusercontent.com' : null,
  );

  String? get token => _token;
  Map<String, dynamic>? get user => _user;
  bool get isAuthenticated => _token != null;
  String get userRole => _user?['role'] ?? 'client';

  Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
    String? userStr = prefs.getString('user_data');
    if (userStr != null) {
      _user = json.decode(userStr);
    }
  }

  Future<void> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
        body: json.encode({
          'email': email,
          'password': password,
          'device_name': kIsWeb ? 'Web' : (defaultTargetPlatform == TargetPlatform.android ? 'Android' : 'iOS'),
        }),
      );

      final data = json.decode(utf8.decode(response.bodyBytes));

      if (response.statusCode == 200) {
        _token = data['token'];
        _user = data['user'];
        await _saveSession();
        notifyListeners();
      } else {
        throw data['message'] ?? 'Identifiants invalides';
      }
    } catch (e) {
      rethrow;
    }
  }

  Future<bool> signInWithGoogle() async {
    try {
      final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();
      if (googleUser == null) return false;

      final GoogleSignInAuthentication googleAuth = await googleUser.authentication;

      final response = await http.post(
        Uri.parse('$baseUrl/auth/google/callback'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
        body: json.encode({
          'token': googleAuth.accessToken,
          'device_name': kIsWeb ? 'Web' : (defaultTargetPlatform == TargetPlatform.android ? 'Android' : 'iOS'),
        }),
      );

      if (response.statusCode == 200) {
        final data = json.decode(utf8.decode(response.bodyBytes));
        _token = data['token'];
        _user = data['user'];
        await _saveSession();
        notifyListeners();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  Future<void> register({
    required String name,
    required String email,
    required String phone,
    required String password,
    required String role,
    int? categoryId,
    int? cityId,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/register'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
        body: json.encode({
          'name': name,
          'email': email,
          'phone': phone,
          'password': password,
          'password_confirmation': password,
          'role': role,
          if (role == 'provider') 'category_id': categoryId,
          if (role == 'provider') 'city_id': cityId,
        }),
      );

      final data = json.decode(utf8.decode(response.bodyBytes));

      if (response.statusCode == 200 || response.statusCode == 201) {
        _token = data['token'];
        _user = data['user'];
        await _saveSession();
        notifyListeners();
      } else {
        if (data['errors'] != null) {
          String errorMsg = "";
          data['errors'].forEach((key, value) {
            errorMsg += "${value[0]}\n";
          });
          throw errorMsg.trim();
        }
        throw data['message'] ?? 'Erreur lors de l\'inscription';
      }
    } catch (e) {
      rethrow;
    }
  }

  void updateUserLocally(Map<String, dynamic> newUser) {
    _user = newUser;
    _saveSession();
    notifyListeners();
  }

  Future<void> _saveSession() async {
    final prefs = await SharedPreferences.getInstance();
    if (_token != null) await prefs.setString('auth_token', _token!);
    if (_user != null) await prefs.setString('user_data', json.encode(_user));
  }

  Future<void> logout() async {
    try {
      if (_token != null) {
        await http.post(
          Uri.parse('$baseUrl/logout'),
          headers: {'Accept': 'application/json', 'Authorization': 'Bearer $_token'},
        );
      }
      await _googleSignIn.signOut();
    } finally {
      _token = null;
      _user = null;
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('auth_token');
      await prefs.remove('user_data');
      notifyListeners();
    }
  }
}

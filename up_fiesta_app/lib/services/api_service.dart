import 'dart:async';
import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'auth_service.dart';
import 'database_service.dart';

class ApiService {
  static const String baseUrl = 'https://upfiesta.com/api';
  final AuthService _auth = AuthService();
  final DatabaseService _db = DatabaseService();

  Map<String, String> get _headers => {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    if (_auth.token != null) 'Authorization': 'Bearer ${_auth.token}',
  };

  // --- PRESTATAIRES ---
  Future<List<Map<String, dynamic>>> getProviders({
    int? categoryId,
    int? cityId,
    String? search,
    String? sortBy,
    String? order,
    int page = 1,
  }) async {
    try {
      final queryParams = {
        if (categoryId != null) 'category_id': categoryId.toString(),
        if (cityId != null) 'city_id': cityId.toString(),
        if (search != null) 'search': search,
        if (sortBy != null) 'sort_by': sortBy,
        if (order != null) 'order': order,
        'page': page.toString(),
      };

      final uri = Uri.parse('$baseUrl/providers').replace(queryParameters: queryParams);
      final response = await http.get(uri, headers: _headers).timeout(const Duration(seconds: 15));

      if (response.statusCode == 200) {
        final data = json.decode(utf8.decode(response.bodyBytes));
        return List<Map<String, dynamic>>.from(data['data'] ?? data);
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  // --- RÉSERVATIONS ---
  Future<List<Map<String, dynamic>>> getBookings() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/bookings'), headers: _headers);
      if (response.statusCode == 200) {
        final data = json.decode(utf8.decode(response.bodyBytes));
        return List<Map<String, dynamic>>.from(data['data'] ?? data);
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<bool> createBooking({required int providerId, required DateTime date, required String message}) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/bookings'),
        headers: _headers,
        body: json.encode({
          'provider_id': providerId,
          'date': date.toIso8601String(),
          'message': message,
        }),
      );
      return response.statusCode == 201 || response.statusCode == 200;
    } catch (e) { return false; }
  }

  Future<bool> updateBookingStatus(int bookingId, String status) async {
    try {
      final response = await http.put(
        Uri.parse('$baseUrl/bookings/$bookingId/status'),
        headers: _headers,
        body: json.encode({'status': status}),
      );
      return response.statusCode == 200;
    } catch (e) { return false; }
  }

  // --- MESSAGERIE (CHAT) ---
  Future<List<Map<String, dynamic>>> getConversations() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/messages'), headers: _headers);
      return response.statusCode == 200 ? List<Map<String, dynamic>>.from(json.decode(utf8.decode(response.bodyBytes))) : [];
    } catch (e) { return []; }
  }

  Future<List<Map<String, dynamic>>> getMessages(int userId) async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/messages/$userId'), headers: _headers);
      if (response.statusCode == 200) {
        final messages = List<Map<String, dynamic>>.from(json.decode(utf8.decode(response.bodyBytes)));
        await _db.cacheMessages(userId, messages);
        return messages;
      }
      throw Exception('Erreur serveur');
    } catch (e) {
      return await _db.getCachedMessages(userId);
    }
  }

  Future<bool> sendMessage(int userId, String content) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/messages/$userId'),
        headers: _headers,
        body: json.encode({'content': content}),
      );
      return response.statusCode == 201;
    } catch (e) { return false; }
  }

  // --- AVIS ---
  Future<List<Map<String, dynamic>>> getReviews(int providerId) async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/providers/$providerId/reviews'), headers: _headers);
      if (response.statusCode == 200) {
        final data = json.decode(utf8.decode(response.bodyBytes));
        return List<Map<String, dynamic>>.from(data['data'] ?? data);
      }
      return [];
    } catch (e) { return []; }
  }

  Future<bool> postReview(int providerId, double rating, String comment) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/providers/$providerId/reviews'),
        headers: _headers,
        body: json.encode({
          'rating': rating,
          'comment': comment,
        }),
      );
      return response.statusCode == 201 || response.statusCode == 200;
    } catch (e) { return false; }
  }

  // --- PROFIL & AUTH ---
  Future<Map<String, dynamic>?> updateProfile(Map<String, String> data) async {
    try {
      final response = await http.put(Uri.parse('$baseUrl/me'), headers: _headers, body: json.encode(data));
      return response.statusCode == 200 ? json.decode(utf8.decode(response.bodyBytes)) : null;
    } catch (e) { return null; }
  }

  Future<Map<String, dynamic>> forgotPassword(String email) async {
    try {
      final response = await http.post(Uri.parse('$baseUrl/forgot-password'), headers: _headers, body: json.encode({'email': email}));
      return json.decode(utf8.decode(response.bodyBytes));
    } catch (e) { return {'message': 'Erreur de connexion.'}; }
  }

  // --- UTILITAIRES (Villes/Catégories) ---
  Future<List<Map<String, dynamic>>> getCategories() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/categories'), headers: _headers);
      return response.statusCode == 200 ? List<Map<String, dynamic>>.from(json.decode(utf8.decode(response.bodyBytes))) : [];
    } catch (e) { return []; }
  }

  Future<List<Map<String, dynamic>>> getCities() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/cities'), headers: _headers);
      return response.statusCode == 200 ? List<Map<String, dynamic>>.from(json.decode(utf8.decode(response.bodyBytes))) : [];
    } catch (e) { return []; }
  }

  // --- DASHBOARD PRESTATAIRE ---
  Future<Map<String, dynamic>?> getProviderStats() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/provider/stats'), headers: _headers);
      return response.statusCode == 200 ? json.decode(utf8.decode(response.bodyBytes)) : null;
    } catch (e) { return null; }
  }

  Future<bool> requestPriceChange(double newPrice) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/provider/request-price-change'),
        headers: _headers,
        body: json.encode({'pending_base_price': newPrice}),
      );
      return response.statusCode == 200;
    } catch (e) { return false; }
  }

  // --- VÉRIFICATION ---
  Future<Map<String, dynamic>> getVerificationStatus() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/provider/verification/status'), headers: _headers);
      return response.statusCode == 200 ? json.decode(utf8.decode(response.bodyBytes)) : {'status': 'not_submitted'};
    } catch (e) { return {'status': 'not_submitted'}; }
  }

  Future<bool> submitVerification({required String cniFrontPath, required String cniBackPath}) async {
    try {
      var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/provider/verification/submit'));
      request.headers.addAll(_headers);
      request.files.add(await http.MultipartFile.fromPath('cni_front', cniFrontPath));
      request.files.add(await http.MultipartFile.fromPath('cni_back', cniBackPath));
      var response = await request.send();
      return response.statusCode == 200;
    } catch (e) { return false; }
  }

  // --- ÉVÉNEMENTS & PAIEMENT ---
  Future<List<Map<String, dynamic>>> getEvents() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/events'), headers: _headers);
      if (response.statusCode == 200) {
        final data = json.decode(utf8.decode(response.bodyBytes));
        return List<Map<String, dynamic>>.from(data['data'] ?? data);
      }
      return [];
    } catch (e) { return []; }
  }

  Future<String?> getPaymentUrl({required int bookingId}) async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/bookings/$bookingId/payment-url'), headers: _headers);
      if (response.statusCode == 200) {
        return json.decode(utf8.decode(response.bodyBytes))['payment_url'];
      }
      return null;
    } catch (e) { return null; }
  }

  Future<bool> updateFcmToken(String token) async {
    try {
      final response = await http.post(Uri.parse('$baseUrl/update-fcm-token'), headers: _headers, body: json.encode({'fcm_token': token}));
      return response.statusCode == 200;
    } catch (e) { return false; }
  }
}

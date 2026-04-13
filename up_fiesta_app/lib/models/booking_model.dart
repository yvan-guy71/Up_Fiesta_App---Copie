enum BookingStatus { pending, confirmed, completed, cancelled }

class Booking {
  final String id;
  final String clientId;
  final String providerId;
  final String eventId;
  final BookingStatus status;
  final double totalPrice;
  final DateTime createdAt;

  Booking({
    required this.id,
    required this.clientId,
    required this.providerId,
    required this.eventId,
    required this.status,
    required this.totalPrice,
    required this.createdAt,
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    return Booking(
      id: json['id'].toString(),
      clientId: json['client_id'].toString(),
      providerId: json['provider_id'].toString(),
      eventId: json['event_id'].toString(),
      status: _parseStatus(json['status']),
      totalPrice: (json['total_price'] ?? 0.0).toDouble(),
      createdAt: DateTime.parse(json['created_at']),
    );
  }

  static BookingStatus _parseStatus(String? status) {
    switch (status?.toLowerCase()) {
      case 'confirmed':
        return BookingStatus.confirmed;
      case 'completed':
        return BookingStatus.completed;
      case 'cancelled':
        return BookingStatus.cancelled;
      default:
        return BookingStatus.pending;
    }
  }
}

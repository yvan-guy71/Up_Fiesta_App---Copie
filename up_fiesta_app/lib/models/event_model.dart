class Event {
  final String id;
  final String name;
  final DateTime date;
  final String location;
  final String description;

  Event({
    required this.id,
    required this.name,
    required this.date,
    required this.location,
    required this.description,
  });

  factory Event.fromJson(Map<String, dynamic> json) {
    return Event(
      id: json['id'].toString(),
      name: json['name'],
      date: DateTime.parse(json['date']),
      location: json['location'],
      description: json['description'] ?? '',
    );
  }
}

enum UserRole { client, provider, admin }

class User {
  final String id;
  final String name;
  final String email;
  final String? phone;
  final UserRole role;
  final String? profileImageUrl;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.role,
    this.profileImageUrl,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'].toString(),
      name: json['name'],
      email: json['email'],
      phone: json['phone'],
      role: _parseRole(json['role']),
      profileImageUrl: json['profile_image_url'],
    );
  }

  static UserRole _parseRole(String? role) {
    switch (role?.toLowerCase()) {
      case 'provider':
        return UserRole.provider;
      case 'admin':
        return UserRole.admin;
      default:
        return UserRole.client;
    }
  }
}

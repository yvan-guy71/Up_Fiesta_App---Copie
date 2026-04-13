class ProviderModel {
  final String userId;
  final int yearsOfExperience;
  final String verificationStatus; // 'pending', 'verified', 'rejected'
  final String bio;
  final List<String> galleryUrls;
  final double rating;
  final List<String> serviceCategories;

  ProviderModel({
    required this.userId,
    required this.yearsOfExperience,
    required this.verificationStatus,
    required this.bio,
    this.galleryUrls = const [],
    this.rating = 0.0,
    this.serviceCategories = const [],
  });

  factory ProviderModel.fromJson(Map<String, dynamic> json) {
    return ProviderModel(
      userId: json['user_id'].toString(),
      yearsOfExperience: json['years_of_experience'] ?? 0,
      verificationStatus: json['verification_status'] ?? 'pending',
      bio: json['bio'] ?? '',
      galleryUrls: List<String>.from(json['gallery'] ?? []),
      rating: (json['rating'] ?? 0.0).toDouble(),
      serviceCategories: List<String>.from(json['categories'] ?? []),
    );
  }
}

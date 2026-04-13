import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../services/api_service.dart';
import 'chat_screen.dart';

class ProviderDetailScreen extends StatefulWidget {
  final Map<String, dynamic> provider;

  const ProviderDetailScreen({super.key, required this.provider});

  @override
  State<ProviderDetailScreen> createState() => _ProviderDetailScreenState();
}

class _ProviderDetailScreenState extends State<ProviderDetailScreen> {
  final ApiService _apiService = ApiService();
  bool _isBooking = false;
  List<Map<String, dynamic>> _reviews = [];
  bool _isLoadingReviews = true;

  @override
  void initState() {
    super.initState();
    _loadReviews();
  }

  Future<void> _loadReviews() async {
    final reviews = await _apiService.getReviews(widget.provider['id']);
    setState(() {
      _reviews = reviews;
      _isLoadingReviews = false;
    });
  }

  Future<void> _selectDateAndBook() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );

    if (picked != null) {
      _showBookingConfirmDialog(picked);
    }
  }

  void _showBookingConfirmDialog(DateTime date) {
    final TextEditingController messageController = TextEditingController();
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Confirmer la réservation', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        content: TextField(
          controller: messageController,
          decoration: const InputDecoration(hintText: 'Détails de l\'événement...'),
          maxLines: 3,
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Annuler')),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(context);
              setState(() => _isBooking = true);
              final success = await _apiService.createBooking(
                providerId: widget.provider['id'],
                date: date,
                message: messageController.text,
              );
              setState(() => _isBooking = false);
              ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(success ? 'Demande envoyée !' : 'Erreur')));
            },
            child: const Text('Confirmer'),
          ),
        ],
      ),
    );
  }

  void _showAddReviewDialog() {
    int selectedRating = 5;
    final TextEditingController commentController = TextEditingController();

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setStateDialog) => AlertDialog(
          title: const Text('Laisser un avis'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(5, (index) => IconButton(
                  icon: Icon(Icons.star, color: index < selectedRating ? Colors.amber : Colors.grey),
                  onPressed: () => setStateDialog(() => selectedRating = index + 1),
                )),
              ),
              TextField(
                controller: commentController,
                decoration: const InputDecoration(hintText: 'Votre commentaire...'),
                maxLines: 2,
              ),
            ],
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text('Annuler')),
            ElevatedButton(
              onPressed: () async {
                final success = await _apiService.postReview(widget.provider['id'], selectedRating.toDouble(), commentController.text);
                if (success) {
                  Navigator.pop(context);
                  _loadReviews();
                }
              },
              child: const Text('Publier'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final provider = widget.provider;
    final String name = provider['company_name'] ?? provider['name'] ?? 'Prestataire';
    final String? imageUrl = provider['logo_url'] ?? provider['image'];
    final List media = provider['media'] ?? [];

    return Scaffold(
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            expandedHeight: 250,
            pinned: true,
            flexibleSpace: FlexibleSpaceBar(
              background: imageUrl != null
                ? CachedNetworkImage(imageUrl: imageUrl, fit: BoxFit.cover)
                : _buildImagePlaceholder(),
            ),
            actions: [
              IconButton(
                icon: const Icon(Icons.chat_bubble_outline, color: Colors.white),
                onPressed: () => Navigator.push(context, MaterialPageRoute(
                  builder: (_) => ChatScreen(
                    otherUser: {
                      'id': provider['user_id'],
                      'name': name,
                    },
                  )
                )),
              ),
            ],
          ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(name, style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.bold, color: const Color(0xFF001489))),
                  const SizedBox(height: 10),
                  Row(
                    children: [
                      const Icon(Icons.star, color: Colors.amber, size: 20),
                      const SizedBox(width: 5),
                      Text('${provider['rating'] ?? '4.9'} (${_reviews.length} avis)', style: const TextStyle(fontWeight: FontWeight.bold)),
                    ],
                  ),
                  const Divider(height: 40),
                  Text('À propos', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                  Text(provider['description'] ?? 'Pas de description.', style: const TextStyle(height: 1.5)),

                  if (media.isNotEmpty) ...[
                    const SizedBox(height: 25),
                    Text('Galerie', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 10),
                    SizedBox(
                      height: 120,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: media.length,
                        itemBuilder: (context, index) => Container(
                          width: 120,
                          margin: const EdgeInsets.only(right: 10),
                          decoration: BoxDecoration(borderRadius: BorderRadius.circular(10)),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(10),
                            child: CachedNetworkImage(imageUrl: media[index]['url'], fit: BoxFit.cover),
                          ),
                        ),
                      ),
                    ),
                  ],

                  const SizedBox(height: 25),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Avis Clients', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                      TextButton(onPressed: _showAddReviewDialog, child: const Text('Donner un avis')),
                    ],
                  ),
                  _isLoadingReviews
                    ? const Center(child: CircularProgressIndicator())
                    : _reviews.isEmpty
                      ? const Text('Aucun avis pour le moment.')
                      : Column(
                          children: _reviews.take(3).map((r) => ListTile(
                            contentPadding: EdgeInsets.zero,
                            title: Text(r['user']?['name'] ?? 'Anonyme'),
                            subtitle: Text(r['comment'] ?? ''),
                            trailing: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                const Icon(Icons.star, color: Colors.amber, size: 16),
                                Text(r['rating'].toString()),
                              ],
                            ),
                          )).toList(),
                        ),
                  const SizedBox(height: 100),
                ],
              ),
            ),
          ),
        ],
      ),
      bottomSheet: Container(
        padding: const EdgeInsets.all(20),
        child: ElevatedButton(
          onPressed: _isBooking ? null : _selectDateAndBook,
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF001489),
            minimumSize: const Size(double.infinity, 55),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          ),
          child: _isBooking ? const CircularProgressIndicator(color: Colors.white) : const Text('Réserver maintenant', style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
        ),
      ),
    );
  }

  Widget _buildImagePlaceholder() {
    return Container(color: Colors.blueGrey[50], child: const Icon(Icons.business, size: 50, color: Color(0xFF001489)));
  }
}

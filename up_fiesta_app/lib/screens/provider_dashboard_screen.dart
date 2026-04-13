import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'bookings_screen.dart';
import 'chat_list_screen.dart';
import 'verification_screen.dart';
import 'price_update_screen.dart';

class ProviderDashboardScreen extends StatefulWidget {
  const ProviderDashboardScreen({super.key});

  @override
  _ProviderDashboardScreenState createState() => _ProviderDashboardScreenState();
}

class _ProviderDashboardScreenState extends State<ProviderDashboardScreen> {
  final ApiService _api = ApiService();
  Map<String, dynamic>? _stats;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadStats();
  }

  Future<void> _loadStats() async {
    final stats = await _api.getProviderStats();
    setState(() {
      _stats = stats;
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tableau de Bord'),
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.message_outlined),
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ChatListScreen())),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _loadStats,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator())
            : SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildWelcomeCard(),
                    const SizedBox(height: 24),
                    _buildStatsGrid(),
                    const SizedBox(height: 24),
                    _buildQuickActions(),
                    const SizedBox(height: 24),
                    const Text('Réservations Récentes', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 12),
                    _buildRecentBookings(),
                  ],
                ),
              ),
      ),
    );
  }

  Widget _buildWelcomeCard() {
    return Card(
      elevation: 4,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
      color: Theme.of(context).primaryColor,
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Row(
          children: [
            const CircleAvatar(
              radius: 30,
              backgroundColor: Colors.white24,
              child: Icon(Icons.business, color: Colors.white, size: 30),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: const [
                  Text('Bonjour !', style: TextStyle(color: Colors.white70, fontSize: 16)),
                  Text('Gérez votre entreprise', style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsGrid() {
    return GridView.count(
      crossAxisCount: 2,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      mainAxisSpacing: 16,
      crossAxisSpacing: 16,
      childAspectRatio: 1.5,
      children: [
        _buildStatCard('Réservations', _stats?['bookings_count']?.toString() ?? '0', Icons.calendar_today, Colors.blue),
        _buildStatCard('Revenus', '${_stats?['revenue']?.toString() ?? '0'} €', Icons.euro, Colors.green),
        _buildStatCard('Services', _stats?['services_count']?.toString() ?? '0', Icons.settings, Colors.orange),
        _buildStatCard('Avis', _stats?['rating']?.toString() ?? '5.0', Icons.star, Colors.amber),
      ],
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(15),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, color: color),
          const SizedBox(height: 8),
          Text(value, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          Text(title, style: TextStyle(color: Colors.grey[600], fontSize: 12)),
        ],
      ),
    );
  }

  Widget _buildQuickActions() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceAround,
      children: [
        _buildActionItem(Icons.verified_user, 'Vérifier', () {
          Navigator.push(context, MaterialPageRoute(builder: (_) => const VerificationScreen()));
        }),
        _buildActionItem(Icons.payments_outlined, 'Tarifs', () {
          final double currentPrice = double.tryParse(_stats?['provider']?['base_price']?.toString() ?? '0') ?? 0.0;
          Navigator.push(context, MaterialPageRoute(builder: (_) => PriceUpdateScreen(currentPrice: currentPrice)));
        }),
        _buildActionItem(Icons.add_photo_alternate, 'Galerie', () {}),
        _buildActionItem(Icons.settings, 'Profil', () {}),
      ],
    );
  }

  Widget _buildActionItem(IconData icon, String label, VoidCallback onTap) {
    return Column(
      children: [
        InkWell(
          onTap: onTap,
          child: CircleAvatar(
            radius: 25,
            backgroundColor: Colors.grey[200],
            child: Icon(icon, color: Theme.of(context).primaryColor),
          ),
        ),
        const SizedBox(height: 4),
        Text(label, style: const TextStyle(fontSize: 12)),
      ],
    );
  }

  Widget _buildRecentBookings() {
    return Card(
      child: ListTile(
        leading: const CircleAvatar(child: Icon(Icons.person)),
        title: const Text('Client : Jean Dupont'),
        subtitle: const Text('Prévu pour le 12 Déc. 2024'),
        trailing: const Icon(Icons.chevron_right),
        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const BookingsScreen())),
      ),
    );
  }
}

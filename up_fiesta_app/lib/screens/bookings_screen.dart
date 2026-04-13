import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:flutter_staggered_animations/flutter_staggered_animations.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import 'login_screen.dart';
import 'payment_screen.dart';

class BookingsScreen extends StatefulWidget {
  const BookingsScreen({super.key});

  @override
  State<BookingsScreen> createState() => _BookingsScreenState();
}

class _BookingsScreenState extends State<BookingsScreen> {
  final ApiService _apiService = ApiService();
  List<Map<String, dynamic>> _bookings = [];
  bool _isLoading = true;
  final String _role = AuthService().userRole;

  @override
  void initState() {
    super.initState();
    if (AuthService().isAuthenticated) {
      _loadBookings();
    } else {
      _isLoading = false;
    }
  }

  Future<void> _loadBookings() async {
    setState(() => _isLoading = true);
    try {
      final data = await _apiService.getBookings();
      setState(() {
        _bookings = data;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _updateStatus(int id, String status) async {
    final success = await _apiService.updateBookingStatus(id, status);
    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Statut mis à jour : $status')),
      );
      _loadBookings();
    }
  }

  Future<void> _handlePayment(int bookingId) async {
    final String? paymentUrl = await _apiService.getPaymentUrl(
      bookingId: bookingId,
    );
    if (paymentUrl != null) {
      final bool? success = await Navigator.push(
        context,
        MaterialPageRoute(builder: (_) => PaymentScreen(url: paymentUrl)),
      );

      if (success == true) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Paiement réussi !'), backgroundColor: Colors.green),
        );
        _loadBookings();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Paiement annulé ou échoué.'), backgroundColor: Colors.red),
        );
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Impossible de générer le lien de paiement.')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    if (!AuthService().isAuthenticated) {
      return _buildLoginRequiredState();
    }

    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: Text('Mes Réservations', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(onPressed: _loadBookings, icon: const Icon(Icons.refresh, color: Color(0xFF001489)))
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _bookings.isEmpty
              ? _buildEmptyState()
              : RefreshIndicator(
                  onRefresh: _loadBookings,
                  child: AnimationLimiter(
                    child: ListView.builder(
                      padding: const EdgeInsets.all(20),
                      physics: const AlwaysScrollableScrollPhysics(),
                      itemCount: _bookings.length,
                      itemBuilder: (context, index) {
                        return AnimationConfiguration.staggeredList(
                          position: index,
                          duration: const Duration(milliseconds: 500),
                          child: SlideAnimation(
                            verticalOffset: 50.0,
                            child: FadeInAnimation(
                              child: _buildBookingCard(_bookings[index]),
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                ),
    );
  }

  Widget _buildLoginRequiredState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(30.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(FontAwesomeIcons.calendarCheck, size: 70, color: const Color(0xFF001489).withOpacity(0.2)),
            const SizedBox(height: 25),
            Text(
              'Suivez vos réservations',
              textAlign: TextAlign.center,
              style: GoogleFonts.poppins(fontSize: 20, fontWeight: FontWeight.bold, color: const Color(0xFF001489)),
            ),
            const SizedBox(height: 10),
            Text(
              'Connectez-vous pour voir et gérer vos réservations en temps réel.',
              textAlign: TextAlign.center,
              style: GoogleFonts.poppins(color: Colors.grey[600]),
            ),
            const SizedBox(height: 30),
            ElevatedButton(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen())),
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF001489),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 15),
              ),
              child: const Text('Se connecter'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBookingCard(Map<String, dynamic> booking) {
    // Si client : on affiche les infos du provider. Si provider : on affiche les infos de l'utilisateur qui a reservé
    final dynamic otherParty = _role == 'provider' ? booking['user'] : (booking['provider'] ?? {});
    final String dateStr = booking['event_date'] ?? booking['booking_date'];
    final DateTime date = DateTime.parse(dateStr);
    final status = booking['status'] ?? 'pending';

    Color statusColor;
    IconData statusIcon;
    String statusText;

    switch (status) {
      case 'confirmed':
        statusColor = Colors.green;
        statusIcon = FontAwesomeIcons.circleCheck;
        statusText = 'Confirmé';
        break;
      case 'cancelled':
        statusColor = Colors.red;
        statusIcon = FontAwesomeIcons.circleXmark;
        statusText = 'Annulé';
        break;
      case 'completed':
        statusColor = const Color(0xFF001489);
        statusIcon = FontAwesomeIcons.flagCheckered;
        statusText = 'Terminé';
        break;
      default:
        statusColor = Colors.orange;
        statusIcon = FontAwesomeIcons.clock;
        statusText = 'En attente';
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 15,
            offset: const Offset(0, 5),
          )
        ],
      ),
      child: Column(
        children: [
          IntrinsicHeight(
            child: Row(
              children: [
                Container(
                  width: 6,
                  decoration: BoxDecoration(
                    color: statusColor,
                    borderRadius: const BorderRadius.only(topLeft: Radius.circular(20)),
                  ),
                ),
                Expanded(
                  child: Padding(
                    padding: const EdgeInsets.all(20.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Text(
                                otherParty['name'] ?? otherParty['company_name'] ?? 'Inconnu',
                                style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 16, color: const Color(0xFF001489)),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                              decoration: BoxDecoration(
                                color: statusColor.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: Row(
                                children: [
                                  Icon(statusIcon, size: 10, color: statusColor),
                                  const SizedBox(width: 5),
                                  Text(
                                    statusText,
                                    style: GoogleFonts.poppins(color: statusColor, fontSize: 10, fontWeight: FontWeight.bold),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 15),
                        Row(
                          children: [
                            Icon(FontAwesomeIcons.calendarDay, size: 14, color: Colors.grey[400]),
                            const SizedBox(width: 10),
                            Text(
                              DateFormat('dd MMMM yyyy', 'fr_FR').format(date),
                              style: GoogleFonts.poppins(color: Colors.grey[600], fontSize: 13),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        if (booking['event_details'] != null || booking['message'] != null) ...[
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Icon(FontAwesomeIcons.quoteLeft, size: 12, color: Colors.grey[300]),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  booking['event_details'] ?? booking['message'],
                                  style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey[500], fontStyle: FontStyle.italic),
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),

          // ACTIONS POUR PRESTATAIRE
          if (_role == 'provider' && status == 'pending') ...[
            const Divider(height: 1),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
              child: Row(
                children: [
                  Expanded(
                    child: TextButton.icon(
                      onPressed: () => _updateStatus(booking['id'], 'cancelled'),
                      icon: const Icon(Icons.close, color: Colors.red, size: 18),
                      label: Text('Refuser', style: GoogleFonts.poppins(color: Colors.red, fontSize: 13, fontWeight: FontWeight.w600)),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: ElevatedButton.icon(
                      onPressed: () => _updateStatus(booking['id'], 'confirmed'),
                      icon: const Icon(Icons.check, size: 18),
                      label: Text('Accepter', style: GoogleFonts.poppins(fontSize: 13, fontWeight: FontWeight.w600)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.green,
                        foregroundColor: Colors.white,
                        elevation: 0,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],

          // ACTIONS POUR CLIENT (Payer ou Annuler)
          if (_role == 'client') ...[
            const Divider(height: 1),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  if (status == 'confirmed')
                    Expanded(
                      child: ElevatedButton.icon(
                        onPressed: () => _handlePayment(booking['id']),
                        icon: const Icon(Icons.payment, size: 18),
                        label: Text('Payer maintenant', style: GoogleFonts.poppins(fontSize: 13, fontWeight: FontWeight.bold)),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFFFF6D00),
                          foregroundColor: Colors.white,
                          elevation: 0,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                        ),
                      ),
                    ),
                  if (status == 'pending')
                    TextButton(
                      onPressed: () => _updateStatus(booking['id'], 'cancelled'),
                      child: Text('Annuler ma demande', style: GoogleFonts.poppins(color: Colors.grey[600], fontSize: 12)),
                    ),
                ],
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(30),
            decoration: BoxDecoration(
              color: const Color(0xFF001489).withOpacity(0.05),
              shape: BoxShape.circle,
            ),
            child: Icon(FontAwesomeIcons.calendarXmark, size: 60, color: const Color(0xFF001489).withOpacity(0.2)),
          ),
          const SizedBox(height: 25),
          Text(
            'Aucune réservation',
            style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold, color: const Color(0xFF001489)),
          ),
          const SizedBox(height: 10),
          Text(
            'Vos demandes apparaîtront ici.',
            style: GoogleFonts.poppins(color: Colors.grey),
          ),
          const SizedBox(height: 20),
          ElevatedButton(
             onPressed: _loadBookings,
             child: const Text('Actualiser'),
          )
        ],
      ),
    );
  }
}

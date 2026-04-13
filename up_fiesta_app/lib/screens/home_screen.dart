import 'package:flutter/material.dart';
import '../services/auth_service.dart';
import 'discovery_screen.dart';
import 'bookings_screen.dart';
import 'profile_screen.dart';
import 'chat_list_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;
  final String _role = AuthService().userRole;

  List<Widget> _getScreens() {
    if (_role == 'admin') {
      return [
        const Center(child: Text('Tableau de bord Admin (Stats)')),
        const ChatListScreen(), // Support client
        const Center(child: Text('Validation Prestataires')),
        const ProfileScreen(),
      ];
    } else if (_role == 'provider') {
      return [
        const Center(child: Text('Mes Services & Planning')),
        const ChatListScreen(), // Messages clients
        const BookingsScreen(), // Demandes reçues
        const ProfileScreen(),
      ];
    } else {
      // Client (Défaut)
      return [
        const DiscoveryScreen(),
        const ChatListScreen(),
        const BookingsScreen(),
        const ProfileScreen(),
      ];
    }
  }

  List<BottomNavigationBarItem> _getNavItems() {
    if (_role == 'admin') {
      return const [
        BottomNavigationBarItem(icon: Icon(Icons.dashboard), label: 'Stats'),
        BottomNavigationBarItem(icon: Icon(Icons.support_agent), label: 'Support'),
        BottomNavigationBarItem(icon: Icon(Icons.verified_user), label: 'Validation'),
        BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profil'),
      ];
    } else if (_role == 'provider') {
      return const [
        BottomNavigationBarItem(icon: Icon(Icons.business_center), label: 'Mon Pro'),
        BottomNavigationBarItem(icon: Icon(Icons.message), label: 'Messages'),
        BottomNavigationBarItem(icon: Icon(Icons.assignment), label: 'Demandes'),
        BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profil'),
      ];
    } else {
      return const [
        BottomNavigationBarItem(icon: Icon(Icons.explore), label: 'Découvrir'),
        BottomNavigationBarItem(icon: Icon(Icons.message), label: 'Messages'),
        BottomNavigationBarItem(icon: Icon(Icons.calendar_today), label: 'Réservations'),
        BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profil'),
      ];
    }
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Image.asset('assets/images/logo.png', height: 40),
        centerTitle: true,
        elevation: 0,
        backgroundColor: Colors.white,
      ),
      body: IndexedStack(
        index: _selectedIndex,
        children: _getScreens(),
      ),
      bottomNavigationBar: BottomNavigationBar(
        items: _getNavItems(),
        currentIndex: _selectedIndex,
        selectedItemColor: const Color(0xFF001489),
        unselectedItemColor: Colors.grey,
        onTap: _onItemTapped,
        type: BottomNavigationBarType.fixed,
        showUnselectedLabels: true,
      ),
    );
  }
}

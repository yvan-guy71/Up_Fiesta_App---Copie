import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../services/auth_service.dart';
import '../main.dart'; // Pour themeNotifier
import 'edit_profile_screen.dart';
import 'legal_screen.dart';
import 'login_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final AuthService _authService = AuthService();

  @override
  Widget build(BuildContext context) {
    final user = _authService.user;
    final bool isDarkMode = themeNotifier.value == ThemeMode.dark;

    return Scaffold(
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            expandedHeight: 200,
            pinned: true,
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFF001489), Color(0xFF000A4D)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const SizedBox(height: 40),
                      CircleAvatar(
                        radius: 40,
                        backgroundColor: Colors.white.withOpacity(0.2),
                        child: Text(
                          user?['name']?[0].toUpperCase() ?? 'U',
                          style: const TextStyle(
                            fontSize: 30,
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        user?['name'] ?? 'Utilisateur',
                        style: GoogleFonts.poppins(
                          color: Colors.white,
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(20.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildSectionTitle('Paramètres'),
                  _buildSettingsTile(
                    icon: isDarkMode ? Icons.dark_mode : Icons.light_mode,
                    title: 'Mode Sombre',
                    trailing: Switch(
                      value: isDarkMode,
                      activeColor: const Color(0xFFFF6D00),
                      onChanged: (value) {
                        setState(() {
                          themeNotifier.value = value
                              ? ThemeMode.dark
                              : ThemeMode.light;
                        });
                      },
                    ),
                  ),
                  _buildSettingsTile(
                    icon: Icons.person_outline,
                    title: 'Modifier mon profil',
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const EditProfileScreen(),
                      ),
                    ),
                  ),

                  const SizedBox(height: 20),
                  _buildSectionTitle('Légal & Support'),
                  _buildSettingsTile(
                    icon: Icons.description_outlined,
                    title: 'Conditions Générales (CGU)',
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const LegalScreen(
                          title: 'CGU',
                          url: 'https://upfiesta.com/cgu',
                        ),
                      ),
                    ),
                  ),
                  _buildSettingsTile(
                    icon: Icons.privacy_tip_outlined,
                    title: 'Politique de Confidentialité',
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => const LegalScreen(
                          title: 'Confidentialité',
                          url: 'https://upfiesta.com/confidentialite',
                        ),
                      ),
                    ),
                  ),
                  _buildSettingsTile(
                    icon: Icons.help_outline,
                    title: 'Aide & Support',
                    onTap: () {
                      // Vous pouvez ajouter un lien vers contact ou support ici
                    },
                  ),

                  const SizedBox(height: 40),
                  if (_authService.isAuthenticated)
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () => _showLogoutConfirmation(context),
                        icon: const Icon(Icons.logout),
                        label: const Text('Se déconnecter'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.red[isDarkMode ? 900 : 50],
                          foregroundColor: Colors.red[isDarkMode ? 100 : 700],
                          padding: const EdgeInsets.symmetric(vertical: 15),
                          elevation: 0,
                        ),
                      ),
                    ),
                  const SizedBox(height: 20),
                  Center(
                    child: Text(
                      'Version 1.0.0',
                      style: GoogleFonts.poppins(
                        color: isDarkMode ? Colors.grey[400] : Colors.grey[600],
                        fontSize: 12,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _showLogoutConfirmation(BuildContext context) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Déconnexion', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
          content: Text('Êtes-vous sûr de vouloir vous déconnecter ?', style: GoogleFonts.poppins()),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('Annuler', style: GoogleFonts.poppins(color: Colors.grey)),
            ),
            TextButton(
              onPressed: () async {
                Navigator.pop(context); // Ferme le dialogue
                await _authService.logout();
                if (mounted) {
                  // On utilise le Navigator global ou le context actuel
                  Navigator.of(context).pushAndRemoveUntil(
                    MaterialPageRoute(builder: (context) => const LoginScreen()),
                    (route) => false,
                  );
                }
              },
              child: Text('Déconnexion', style: GoogleFonts.poppins(color: Colors.red, fontWeight: FontWeight.bold)),
            ),
          ],
        );
      },
    );
  }

  Widget _buildSectionTitle(String title) {
    final isDarkMode = themeNotifier.value == ThemeMode.dark;
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 10),
      child: Text(
        title,
        style: GoogleFonts.poppins(
          fontSize: 14,
          fontWeight: FontWeight.bold,
          color: isDarkMode ? Colors.grey[300] : Colors.grey[700],
        ),
      ),
    );
  }

  Widget _buildSettingsTile({
    required IconData icon,
    required String title,
    Widget? trailing,
    VoidCallback? onTap,
  }) {
    return Card(
      elevation: 0,
      color: Theme.of(context).cardColor,
      margin: const EdgeInsets.only(bottom: 10),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(15),
        side: BorderSide(color: Colors.grey[100]!),
      ),
      child: ListTile(
        onTap: onTap,
        leading: Icon(icon, color: const Color(0xFF001489)),
        title: Text(
          title,
          style: GoogleFonts.poppins(fontSize: 15, fontWeight: FontWeight.w500),
        ),
        trailing: trailing ?? const Icon(Icons.chevron_right, size: 20),
      ),
    );
  }
}

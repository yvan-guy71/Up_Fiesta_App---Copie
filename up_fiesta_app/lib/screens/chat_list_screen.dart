import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import 'chat_screen.dart';
import 'login_screen.dart';

class ChatListScreen extends StatefulWidget {
  const ChatListScreen({super.key});

  @override
  State<ChatListScreen> createState() => _ChatListScreenState();
}

class _ChatListScreenState extends State<ChatListScreen> {
  final ApiService _apiService = ApiService();
  List<Map<String, dynamic>> _conversations = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    if (AuthService().isAuthenticated) {
      _loadConversations();
    } else {
      _isLoading = false;
    }
  }

  Future<void> _loadConversations() async {
    setState(() => _isLoading = true);
    final data = await _apiService.getConversations();
    setState(() {
      _conversations = data;
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (!AuthService().isAuthenticated) {
      return _buildLoginRequiredState();
    }

    final myId = AuthService().user?['id'];

    return Scaffold(
      appBar: AppBar(
        title: Text('Messages', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        elevation: 0,
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF001489),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadConversations,
              child: _conversations.isEmpty
                  ? _buildEmptyState()
                  : ListView.separated(
                      padding: const EdgeInsets.all(16),
                      physics: const BouncingScrollPhysics(),
                      itemCount: _conversations.length,
                      separatorBuilder: (context, index) => const Divider(height: 1, color: Color(0xFFF0F0F0)),
                      itemBuilder: (context, index) {
                        final conv = _conversations[index];
                        final otherUser = conv['sender_id'] == myId ? conv['receiver'] : conv['sender'];

                        return ListTile(
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (context) => ChatScreen(
                                  otherUser: otherUser,
                                ),
                              ),
                            ).then((_) => _loadConversations());
                          },
                          leading: CircleAvatar(
                            radius: 25,
                            backgroundColor: const Color(0xFF001489).withOpacity(0.1),
                            child: Text(
                              otherUser['name'][0].toUpperCase(),
                              style: const TextStyle(color: Color(0xFF001489), fontWeight: FontWeight.bold, fontSize: 18),
                            ),
                          ),
                          title: Text(
                            otherUser['name'],
                            style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 15),
                          ),
                          subtitle: Text(
                            conv['content'],
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: GoogleFonts.poppins(fontSize: 13, color: Colors.grey[600]),
                          ),
                          trailing: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              if (conv['is_read'] == 0 && conv['receiver_id'] == myId)
                                Container(
                                  width: 10,
                                  height: 10,
                                  decoration: const BoxDecoration(
                                    color: Color(0xFFFF6D00),
                                    shape: BoxShape.circle,
                                  ),
                                ),
                            ],
                          ),
                        );
                      },
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
            Container(
              padding: const EdgeInsets.all(30),
              decoration: BoxDecoration(
                color: const Color(0xFF001489).withOpacity(0.05),
                shape: BoxShape.circle,
              ),
              child: Icon(FontAwesomeIcons.comments, size: 60, color: const Color(0xFF001489).withOpacity(0.2)),
            ),
            const SizedBox(height: 25),
            Text(
              'Discutez avec vos prestataires',
              textAlign: TextAlign.center,
              style: GoogleFonts.poppins(fontSize: 20, fontWeight: FontWeight.bold, color: const Color(0xFF001489)),
            ),
            const SizedBox(height: 10),
            Text(
              'Connectez-vous pour poser vos questions et organiser vos événements.',
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
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              ),
              child: Text('Se connecter', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(FontAwesomeIcons.commentSlash, size: 60, color: Colors.grey[200]),
          const SizedBox(height: 20),
          Text(
            'Aucune conversation',
            style: GoogleFonts.poppins(color: Colors.grey[500], fontSize: 16, fontWeight: FontWeight.w500),
          ),
        ],
      ),
    );
  }
}

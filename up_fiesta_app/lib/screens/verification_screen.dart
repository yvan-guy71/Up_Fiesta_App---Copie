import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import '../services/api_service.dart';

class VerificationScreen extends StatefulWidget {
  const VerificationScreen({super.key});

  @override
  State<VerificationScreen> createState() => _VerificationScreenState();
}

class _VerificationScreenState extends State<VerificationScreen> {
  final ApiService _api = ApiService();
  final ImagePicker _picker = ImagePicker();

  String _status = 'loading';
  String? _rejectionReason;

  File? _cniFront;
  File? _cniBack;
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _loadStatus();
  }

  Future<void> _loadStatus() async {
    final data = await _api.getVerificationStatus();
    setState(() {
      _status = data['status'];
      _rejectionReason = data['reason'];
    });
  }

  Future<void> _pickImage(bool isFront) async {
    final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      setState(() {
        if (isFront) _cniFront = File(image.path);
        else _cniBack = File(image.path);
      });
    }
  }

  Future<void> _submit() async {
    if (_cniFront == null || _cniBack == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Veuillez ajouter les deux faces de votre CNI')));
      return;
    }

    setState(() => _isSubmitting = true);
    final success = await _api.submitVerification(
      cniFrontPath: _cniFront!.path,
      cniBackPath: _cniBack!.path,
    );

    setState(() => _isSubmitting = false);
    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Documents envoyés !'), backgroundColor: Colors.green));
      _loadStatus();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Erreur lors de l\'envoi.'), backgroundColor: Colors.red));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Vérification du compte')),
      body: _status == 'loading'
        ? const Center(child: CircularProgressIndicator())
        : SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildStatusBanner(),
                const SizedBox(height: 30),
                if (_status == 'not_submitted' || _status == 'rejected') ...[
                  const Text('Veuillez fournir une pièce d\'identité valide (CNI ou Passeport)', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 20),
                  _buildImagePickerCard('Recto de la CNI', _cniFront, () => _pickImage(true)),
                  const SizedBox(height: 15),
                  _buildImagePickerCard('Verso de la CNI', _cniBack, () => _pickImage(false)),
                  const SizedBox(height: 40),
                  SizedBox(
                    width: double.infinity,
                    height: 55,
                    child: ElevatedButton(
                      onPressed: _isSubmitting ? null : _submit,
                      style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF001489), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
                      child: _isSubmitting ? const CircularProgressIndicator(color: Colors.white) : const Text('Soumettre pour vérification', style: TextStyle(color: Colors.white, fontSize: 16)),
                    ),
                  ),
                ] else if (_status == 'pending')
                  const Center(child: Text('Vos documents sont en cours d\'analyse. Vous recevrez une notification dès que votre compte sera validé.'))
                else if (_status == 'approved')
                  const Center(child: Icon(Icons.verified, color: Colors.green, size: 100)),
              ],
            ),
          ),
    );
  }

  Widget _buildStatusBanner() {
    Color color = Colors.grey;
    String text = 'Non soumis';
    if (_status == 'pending') { color = Colors.orange; text = 'En attente de validation'; }
    if (_status == 'approved') { color = Colors.green; text = 'Compte vérifié'; }
    if (_status == 'rejected') { color = Colors.red; text = 'Refusé : $_rejectionReason'; }

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(10), border: Border.all(color: color)),
      child: Text(text, style: TextStyle(color: color, fontWeight: FontWeight.bold), textAlign: TextAlign.center),
    );
  }

  Widget _buildImagePickerCard(String title, File? image, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      child: Container(
        height: 150,
        width: double.infinity,
        decoration: BoxDecoration(border: Border.all(color: Colors.grey.shade300), borderRadius: BorderRadius.circular(15), color: Colors.grey.shade50),
        child: image != null
          ? ClipRRect(borderRadius: BorderRadius.circular(15), child: Image.file(image, fit: BoxFit.cover))
          : Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.add_a_photo, size: 40, color: Colors.grey),
                const SizedBox(height: 10),
                Text(title, style: const TextStyle(color: Colors.grey)),
              ],
            ),
      ),
    );
  }
}

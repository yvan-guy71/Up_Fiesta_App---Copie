import 'package:flutter/material.dart';
import '../services/api_service.dart';

class PriceUpdateScreen extends StatefulWidget {
  final double currentPrice;

  const PriceUpdateScreen({super.key, required this.currentPrice});

  @override
  State<PriceUpdateScreen> createState() => _PriceUpdateScreenState();
}

class _PriceUpdateScreenState extends State<PriceUpdateScreen> {
  final ApiService _api = ApiService();
  final TextEditingController _priceController = TextEditingController();
  bool _isLoading = false;
  String? _pendingPrice;
  String? _status;

  @override
  void initState() {
    super.initState();
    _fetchStatus();
  }

  Future<void> _fetchStatus() async {
    final stats = await _api.getProviderStats();
    if (stats != null && stats['provider'] != null) {
      setState(() {
        _pendingPrice = stats['provider']['pending_base_price']?.toString();
        _status = stats['provider']['price_change_status'];
      });
    }
  }

  Future<void> _submitRequest() async {
    if (_priceController.text.isEmpty) return;

    setState(() => _isLoading = true);
    final success = await _api.requestPriceChange(double.parse(_priceController.text));
    setState(() => _isLoading = false);

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Demande envoyée avec succès !')),
      );
      _fetchStatus();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Erreur lors de l\'envoi.')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Modifier mes tarifs')),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Prix de base actuel',
              style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey),
            ),
            Text(
              '${widget.currentPrice.toInt()} F CFA',
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 30),

            if (_status == 'pending')
              Container(
                padding: const EdgeInsets.all(15),
                decoration: BoxDecoration(
                  color: Colors.orange.shade50,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: Colors.orange),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.hourglass_empty, color: Colors.orange),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        'Une demande de ${_pendingPrice} F CFA est en attente de validation par l\'admin.',
                        style: const TextStyle(color: Colors.orange),
                      ),
                    ),
                  ],
                ),
              )
            else ...[
              const Text(
                'Proposer un nouveau prix',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 10),
              TextField(
                controller: _priceController,
                keyboardType: TextInputType.number,
                decoration: const InputDecoration(
                  hintText: 'Ex: 25000',
                  suffixText: 'F CFA',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submitRequest,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFFF5A5F),
                    foregroundColor: Colors.white,
                  ),
                  child: _isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text('Soumettre pour approbation'),
                ),
              ),
              const SizedBox(height: 15),
              const Text(
                'Note : Le changement de prix ne sera effectif qu\'après validation par l\'équipe Upfiesta.',
                style: TextStyle(fontSize: 12, color: Colors.grey, fontStyle: FontStyle.italic),
              ),
            ],
          ],
        ),
      ),
    );
  }
}




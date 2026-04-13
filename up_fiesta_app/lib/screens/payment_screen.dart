import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

class PaymentScreen extends StatefulWidget {
  final String url;

  const PaymentScreen({super.key, required this.url});

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  late final WebViewController _controller;

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (String url) {
            // Détecter la redirection de succès ou d'échec
            if (url.contains('callback') || url.contains('success')) {
              Navigator.pop(context, true); // Succès
            } else if (url.contains('cancel') || url.contains('fail')) {
              Navigator.pop(context, false); // Échec
            }
          },
        ),
      )
      ..loadRequest(Uri.parse(widget.url));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Paiement Sécurisé'),
        centerTitle: true,
      ),
      body: WebViewWidget(controller: _controller),
    );
  }
}

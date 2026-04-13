import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/auth_service.dart';
import '../services/api_service.dart';
import 'home_screen.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _api = ApiService();
  final _auth = AuthService();

  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  String _selectedRole = 'client';
  int? _selectedCategoryId;
  int? _selectedCityId;
  bool _isLoading = false;
  bool _isDataLoading = true;
  bool _obscurePassword = true;
  bool _obscureConfirm = true;

  List<Map<String, dynamic>> _categories = [];
  List<Map<String, dynamic>> _cities = [];

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isDataLoading = true);
    try {
      final cats = await _api.getCategories();
      final cities = await _api.getCities();
      setState(() {
        _categories = cats;
        _cities = cities;
      });
    } catch (e) {
      debugPrint('Erreur chargement données: $e');
    } finally {
      setState(() => _isDataLoading = false);
    }
  }

  Future<void> _handleRegister() async {
    if (!_formKey.currentState!.validate()) return;

    if (_passwordController.text != _confirmPasswordController.text) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Les mots de passe ne correspondent pas.')),
      );
      return;
    }

    if (_selectedRole == 'provider' && (_selectedCategoryId == null || _selectedCityId == null)) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Veuillez choisir votre catégorie et votre ville.')),
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      await _auth.register(
        name: _nameController.text,
        email: _emailController.text,
        phone: _phoneController.text,
        password: _passwordController.text,
        role: _selectedRole,
        categoryId: _selectedCategoryId,
        cityId: _selectedCityId,
      );

      if (mounted) {
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (context) => const HomeScreen()),
          (route) => false,
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString()), backgroundColor: Colors.red),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(title: const Text('Créer un compte'), elevation: 0),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Rejoignez Upfiesta',
                  style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.bold, color: primaryColor),
                ),
                const SizedBox(height: 8),
                Text('Remplissez les informations ci-dessous pour commencer.',
                  style: TextStyle(color: Colors.grey.shade600)),
                const SizedBox(height: 25),

                const Text('Je m\'inscris en tant que :', style: TextStyle(fontWeight: FontWeight.bold)),
                const SizedBox(height: 10),
                _buildRoleSelector(primaryColor),
                const SizedBox(height: 20),

                if (_selectedRole == 'provider') ...[
                  _buildDropdownField(
                    label: 'Catégorie de service',
                    value: _selectedCategoryId,
                    items: _categories,
                    onChanged: (val) => setState(() => _selectedCategoryId = val),
                    icon: Icons.category,
                    isLoading: _isDataLoading,
                  ),
                  const SizedBox(height: 16),
                  _buildDropdownField(
                    label: 'Votre ville',
                    value: _selectedCityId,
                    items: _cities,
                    onChanged: (val) => setState(() => _selectedCityId = val),
                    icon: Icons.location_city,
                    isLoading: _isDataLoading,
                  ),
                  const SizedBox(height: 20),
                ],

                _buildField(
                  _nameController,
                  'Nom complet',
                  Icons.person,
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Champ requis';
                    if (v.length < 3) return 'Nom trop court';
                    return null;
                  },
                ),
                _buildField(
                  _emailController,
                  'Email',
                  Icons.email,
                  type: TextInputType.emailAddress,
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Champ requis';
                    if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(v)) {
                      return 'Email invalide';
                    }
                    return null;
                  },
                ),
                _buildField(
                  _phoneController,
                  'Téléphone',
                  Icons.phone,
                  type: TextInputType.phone,
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Champ requis';
                    if (v.length < 8) return 'Numéro invalide';
                    return null;
                  },
                ),
                _buildField(
                  _passwordController,
                  'Mot de passe',
                  Icons.lock,
                  obscure: _obscurePassword,
                  suffixIcon: IconButton(
                    icon: Icon(_obscurePassword ? Icons.visibility_off : Icons.visibility),
                    onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                  ),
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Champ requis';
                    if (v.length < 8) return 'Minimum 8 caractères';
                    return null;
                  },
                ),
                _buildField(
                  _confirmPasswordController,
                  'Confirmer le mot de passe',
                  Icons.lock,
                  obscure: _obscureConfirm,
                  suffixIcon: IconButton(
                    icon: Icon(_obscureConfirm ? Icons.visibility_off : Icons.visibility),
                    onPressed: () => setState(() => _obscureConfirm = !_obscureConfirm),
                  ),
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Champ requis';
                    if (v != _passwordController.text) return 'Les mots de passe diffèrent';
                    return null;
                  },
                ),

                const SizedBox(height: 30),
                SizedBox(
                  width: double.infinity,
                  height: 55,
                  child: ElevatedButton(
                    onPressed: (_isLoading || (_selectedRole == 'provider' && _isDataLoading)) ? null : _handleRegister,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: primaryColor,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      elevation: 2,
                    ),
                    child: _isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : const Text('S\'inscrire', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  ),
                ),
                const SizedBox(height: 20),

                Row(
                  children: [
                    Expanded(child: Divider(color: Colors.grey.shade400)),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: Text('OU', style: TextStyle(color: Colors.grey.shade600, fontSize: 12)),
                    ),
                    Expanded(child: Divider(color: Colors.grey.shade400)),
                  ],
                ),
                const SizedBox(height: 20),

                SizedBox(
                  width: double.infinity,
                  height: 55,
                  child: OutlinedButton.icon(
                    onPressed: _isLoading ? null : () async {
                      setState(() => _isLoading = true);
                      final success = await _auth.signInWithGoogle();
                      setState(() => _isLoading = false);
                      if (success && mounted) {
                        Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => const HomeScreen()));
                      }
                    },
                    icon: const Icon(Icons.g_mobiledata, size: 30),
                    label: const Text('S\'inscrire avec Google', style: TextStyle(color: Colors.black87)),
                    style: OutlinedButton.styleFrom(
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      side: BorderSide(color: Colors.grey.shade300),
                    ),
                  ),
                ),

                const SizedBox(height: 30),

                Center(
                  child: TextButton(
                    onPressed: () => Navigator.pop(context),
                    child: RichText(
                      text: TextSpan(
                        text: 'Déjà un compte ? ',
                        style: const TextStyle(color: Colors.black54),
                        children: [
                          TextSpan(
                            text: 'Se connecter',
                            style: TextStyle(color: primaryColor, fontWeight: FontWeight.bold),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 20),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRoleSelector(Color primaryColor) {
    return Row(
      children: [
        _roleButton('client', 'Client', Icons.person_outline, primaryColor),
        const SizedBox(width: 12),
        _roleButton('provider', 'Prestataire', Icons.business_center_outlined, primaryColor),
      ],
    );
  }

  Widget _roleButton(String role, String label, IconData icon, Color primaryColor) {
    bool isSelected = _selectedRole == role;
    return Expanded(
      child: InkWell(
        onTap: () => setState(() => _selectedRole = role),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: isSelected ? primaryColor : Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: isSelected ? primaryColor : Colors.grey.shade300),
          ),
          child: Column(
            children: [
              Icon(icon, color: isSelected ? Colors.white : Colors.grey),
              const SizedBox(height: 4),
              Text(label, style: TextStyle(color: isSelected ? Colors.white : Colors.grey, fontWeight: FontWeight.bold)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildDropdownField({
    required String label,
    required int? value,
    required List<Map<String, dynamic>> items,
    required Function(int?) onChanged,
    required IconData icon,
    bool isLoading = false,
  }) {
    return InputDecorator(
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: isLoading
          ? Container(
              padding: const EdgeInsets.all(12),
              width: 24,
              height: 24,
              child: const CircularProgressIndicator(strokeWidth: 2)
            )
          : Icon(icon),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<int>(
          value: value,
          isExpanded: true,
          hint: Text(isLoading ? 'Chargement...' : 'Choisir...'),
          items: items.map((item) {
            return DropdownMenuItem<int>(
              value: item['id'],
              child: Text(item['name'] ?? ''),
            );
          }).toList(),
          onChanged: isLoading ? null : onChanged,
        ),
      ),
    );
  }

  Widget _buildField(
    TextEditingController controller,
    String label,
    IconData icon, {
    bool obscure = false,
    TextInputType type = TextInputType.text,
    String? Function(String?)? validator,
    Widget? suffixIcon,
  }) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: TextFormField(
        controller: controller,
        obscureText: obscure,
        keyboardType: type,
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: Icon(icon),
          suffixIcon: suffixIcon,
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
        ),
        validator: validator ?? (v) => v!.isEmpty ? 'Champ requis' : null,
      ),
    );
  }
}




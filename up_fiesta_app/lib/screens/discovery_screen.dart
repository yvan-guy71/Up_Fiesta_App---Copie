import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:flutter_staggered_animations/flutter_staggered_animations.dart';
import 'dart:async';
import '../widgets/provider_card.dart';
import '../widgets/skeleton.dart';
import '../services/api_service.dart';
import '../services/auth_service.dart';
import 'provider_detail_screen.dart';

class DiscoveryScreen extends StatefulWidget {
  const DiscoveryScreen({super.key});

  @override
  State<DiscoveryScreen> createState() => _DiscoveryScreenState();
}

class _DiscoveryScreenState extends State<DiscoveryScreen> {
  final ApiService _apiService = ApiService();
  final ScrollController _scrollController = ScrollController();
  final TextEditingController _searchController = TextEditingController();
  Timer? _debounce;

  List<Map<String, dynamic>> _providers = [];
  List<Map<String, dynamic>> _categories = [];
  List<Map<String, dynamic>> _cities = [];

  int? _selectedCategoryId;
  int? _selectedCityId;
  String? _sortBy;
  String? _order;
  bool _isLoading = true;
  bool _isPaginationLoading = false;
  int _currentPage = 1;
  bool _hasMoreData = true;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _loadInitialData();
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _searchController.dispose();
    _debounce?.cancel();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200 &&
        !_isPaginationLoading &&
        _hasMoreData) {
      _loadMoreProviders();
    }
  }

  Future<void> _loadInitialData() async {
    setState(() {
      _isLoading = true;
      _currentPage = 1;
      _hasMoreData = true;
    });
    try {
      final results = await Future.wait([
        _apiService.getCategories(),
        _apiService.getCities(),
        _fetchProvidersData(page: 1),
      ]);
      setState(() {
        _categories = List<Map<String, dynamic>>.from(results[0]);
        _cities = List<Map<String, dynamic>>.from(results[1]);
        _providers = List<Map<String, dynamic>>.from(results[2]);
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _errorMessage = "Erreur de connexion";
        _isLoading = false;
      });
    }
  }

  Future<List<Map<String, dynamic>>> _fetchProvidersData({int page = 1}) async {
    return await _apiService.getProviders(
      categoryId: _selectedCategoryId,
      cityId: _selectedCityId,
      search: _searchController.text.isNotEmpty ? _searchController.text : null,
      sortBy: _sortBy,
      order: _order,
      page: page,
    );
  }

  Future<void> _loadMoreProviders() async {
    if (_isPaginationLoading || !_hasMoreData) return;
    setState(() => _isPaginationLoading = true);

    final nextPage = _currentPage + 1;
    try {
      final newProviders = await _fetchProvidersData(page: nextPage);
      if (newProviders.isEmpty) {
        setState(() => _hasMoreData = false);
      } else {
        setState(() {
          _currentPage = nextPage;
          // Éviter les doublons par ID
          for (var p in newProviders) {
            if (!_providers.any((existing) => existing['id'] == p['id'])) {
              _providers.add(p);
            }
          }
        });
      }
    } catch (e) {
      debugPrint("Erreur pagination: $e");
    } finally {
      setState(() => _isPaginationLoading = false);
    }
  }

  void _onSearchChanged(String query) {
    if (_debounce?.isActive ?? false) _debounce!.cancel();
    _debounce = Timer(const Duration(milliseconds: 500), () {
      _loadInitialData();
    });
  }

  @override
  Widget build(BuildContext context) {
    final userName = AuthService().user?['name']?.split(' ')[0] ?? 'Invité';

    return RefreshIndicator(
      onRefresh: _loadInitialData,
      color: const Color(0xFF001489),
      child: CustomScrollView(
        controller: _scrollController,
        physics: const AlwaysScrollableScrollPhysics(parent: BouncingScrollPhysics()),
        slivers: [
          _buildHeader(userName),
          _buildSearchBar(),
          _buildSortAndResetOptions(),
          _buildCitiesSelector(),
          _buildCategoriesSelector(),
          _buildProvidersGrid(),
          if (_isPaginationLoading)
            const SliverToBoxAdapter(
              child: Padding(
                padding: EdgeInsets.symmetric(vertical: 20),
                child: Center(child: CircularProgressIndicator()),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildHeader(String name) {
    return SliverToBoxAdapter(
      child: Padding(
        padding: const EdgeInsets.fromLTRB(25, 20, 25, 10),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Bonjour $name 👋', style: GoogleFonts.poppins(fontSize: 14, color: Colors.grey[600])),
                Text('Prêt pour la fête ?', style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.bold, color: const Color(0xFF001489))),
              ],
            ),
            _buildNotificationIcon(),
          ],
        ),
      ),
    );
  }

  Widget _buildNotificationIcon() {
    return Container(
      decoration: BoxDecoration(color: const Color(0xFF001489).withOpacity(0.05), borderRadius: BorderRadius.circular(15)),
      child: IconButton(
        icon: const Icon(FontAwesomeIcons.bell, size: 20, color: Color(0xFF001489)),
        onPressed: () {},
      ),
    );
  }

  Widget _buildSearchBar() {
    return SliverToBoxAdapter(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 25.0, vertical: 10),
        child: Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            boxShadow: [BoxShadow(color: const Color(0xFF001489).withOpacity(0.06), blurRadius: 25, offset: const Offset(0, 8))],
          ),
          child: TextField(
            controller: _searchController,
            onChanged: _onSearchChanged,
            decoration: InputDecoration(
              hintText: 'DJ, Photographe, Traiteur...',
              hintStyle: GoogleFonts.poppins(fontSize: 14, color: Colors.grey[400]),
              prefixIcon: const Icon(FontAwesomeIcons.magnifyingGlass, color: Color(0xFF001489), size: 18),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(vertical: 18),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildSortAndResetOptions() {
    return SliverToBoxAdapter(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 5),
        child: SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: Row(
            children: [
              _sortChip('Prix ↑', 'price', 'asc'),
              const SizedBox(width: 8),
              _sortChip('Prix ↓', 'price', 'desc'),
              const SizedBox(width: 8),
              _sortChip('Note', 'rating', 'desc'),
              const SizedBox(width: 15),
              if (_selectedCategoryId != null || _selectedCityId != null || _sortBy != null || _searchController.text.isNotEmpty)
                TextButton.icon(
                  onPressed: _resetFilters,
                  icon: const Icon(Icons.refresh, size: 18, color: Colors.redAccent),
                  label: Text('Réinitialiser', style: GoogleFonts.poppins(color: Colors.redAccent, fontSize: 12)),
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _sortChip(String label, String? field, String? order) {
    bool isSelected = _sortBy == field && _order == order;
    return ChoiceChip(
      label: Text(label, style: TextStyle(fontSize: 11, color: isSelected ? Colors.white : Colors.black87)),
      selected: isSelected,
      onSelected: (selected) {
        setState(() {
          _sortBy = selected ? field : null;
          _order = selected ? order : null;
        });
        _loadInitialData();
      },
      selectedColor: const Color(0xFF001489),
      backgroundColor: Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15), side: BorderSide(color: isSelected ? Colors.transparent : Colors.grey.shade300)),
    );
  }

  void _resetFilters() {
    setState(() {
      _selectedCategoryId = null;
      _selectedCityId = null;
      _sortBy = null;
      _order = null;
      _searchController.clear();
    });
    _loadInitialData();
  }

  Widget _buildCitiesSelector() {
    if (_isLoading && _cities.isEmpty) {
      return SliverToBoxAdapter(
        child: SizedBox(
          height: 45,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 20),
            itemCount: 5,
            itemBuilder: (context, index) => const Padding(
              padding: EdgeInsets.only(right: 8.0),
              child: Skeleton(height: 35, width: 100, borderRadius: 20),
            ),
          ),
        ),
      );
    }
    if (_cities.isEmpty) return const SliverToBoxAdapter(child: SizedBox.shrink());
    return SliverToBoxAdapter(
      child: SizedBox(
        height: 45,
        child: ListView.builder(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 20),
          itemCount: _cities.length + 1,
          itemBuilder: (context, index) {
            final bool isAll = index == 0;
            final city = isAll ? null : _cities[index - 1];
            final bool isSelected = _selectedCityId == (isAll ? null : city!['id']);

            return Padding(
              padding: const EdgeInsets.only(right: 8.0),
              child: ChoiceChip(
                label: Text(isAll ? 'Toutes les villes' : city!['name']),
                selected: isSelected,
                onSelected: (selected) {
                  setState(() => _selectedCityId = selected ? (isAll ? null : city!['id']) : null);
                  _loadInitialData();
                },
                selectedColor: const Color(0xFF001489),
                backgroundColor: Colors.white,
                labelStyle: TextStyle(color: isSelected ? Colors.white : Colors.black87),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
              ),
            );
          },
        ),
      ),
    );
  }

  Widget _buildCategoriesSelector() {
    if (_isLoading && _categories.isEmpty) {
      return SliverToBoxAdapter(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Padding(
              padding: EdgeInsets.fromLTRB(25, 20, 25, 10),
              child: Skeleton(height: 25, width: 120),
            ),
            SizedBox(
              height: 95,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 20),
                itemCount: 6,
                itemBuilder: (context, index) => const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 8.0),
                  child: Skeleton(height: 55, width: 55, borderRadius: 15),
                ),
              ),
            ),
          ],
        ),
      );
    }
    return SliverToBoxAdapter(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(25, 20, 25, 10),
            child: Text('Catégories', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
          ),
          SizedBox(
            height: 95,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 20),
              itemCount: _categories.length + 1,
              itemBuilder: (context, index) {
                if (index == 0) return _buildCatItem(null, 'Tous', FontAwesomeIcons.layerGroup);
                final cat = _categories[index - 1];
                return _buildCatItem(cat['id'], cat['name'], _getCategoryIcon(cat['name']));
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCatItem(int? id, String label, IconData icon) {
    bool isSelected = _selectedCategoryId == id;
    return GestureDetector(
      onTap: () {
        setState(() => _selectedCategoryId = id);
        _loadInitialData();
      },
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 8.0),
        child: Column(
          children: [
            AnimatedContainer(
              duration: const Duration(milliseconds: 300),
              width: 55, height: 55,
              decoration: BoxDecoration(
                color: isSelected ? const Color(0xFF001489) : Colors.white,
                borderRadius: BorderRadius.circular(15),
                border: Border.all(color: isSelected ? const Color(0xFF001489) : Colors.grey[200]!),
              ),
              child: Icon(icon, size: 18, color: isSelected ? Colors.white : const Color(0xFF001489)),
            ),
            const SizedBox(height: 5),
            Text(label, style: GoogleFonts.poppins(fontSize: 10, color: isSelected ? const Color(0xFF001489) : Colors.grey[600], fontWeight: isSelected ? FontWeight.bold : FontWeight.normal)),
          ],
        ),
      ),
    );
  }

  Widget _buildProvidersGrid() {
    if (_isLoading) {
      return SliverPadding(
        padding: const EdgeInsets.fromLTRB(25, 10, 25, 20),
        sliver: SliverGrid(
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            childAspectRatio: 0.75,
            crossAxisSpacing: 15,
            mainAxisSpacing: 15,
          ),
          delegate: SliverChildBuilderDelegate(
            (context, index) => const ProviderCardSkeleton(),
            childCount: 6,
          ),
        ),
      );
    }
    if (_providers.isEmpty) return SliverFillRemaining(child: _buildEmptyState());

    return SliverPadding(
      padding: const EdgeInsets.fromLTRB(25, 10, 25, 20),
      sliver: SliverGrid(
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(crossAxisCount: 2, childAspectRatio: 0.75, crossAxisSpacing: 15, mainAxisSpacing: 15),
        delegate: SliverChildBuilderDelegate(
          (context, index) => GestureDetector(
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ProviderDetailScreen(provider: _providers[index]))),
            child: ProviderCard(provider: _providers[index]),
          ),
          childCount: _providers.length,
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
      Icon(FontAwesomeIcons.faceFrown, size: 50, color: Colors.grey[300]),
      const SizedBox(height: 15),
      Text('Aucun prestataire trouvé', style: GoogleFonts.poppins(color: Colors.grey[500])),
    ]));
  }

  IconData _getCategoryIcon(String name) {
    name = name.toLowerCase();
    if (name.contains('dj')) return FontAwesomeIcons.music;
    if (name.contains('photo')) return FontAwesomeIcons.camera;
    if (name.contains('traiteur')) return FontAwesomeIcons.utensils;
    if (name.contains('déco')) return FontAwesomeIcons.wandMagicSparkles;
    if (name.contains('salle')) return FontAwesomeIcons.houseUser;
    return FontAwesomeIcons.star;
  }
}

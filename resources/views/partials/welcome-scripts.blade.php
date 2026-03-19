<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const content = document.getElementById('mobile-menu-content');
        
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('-translate-x-full');
            }, 10);
        } else {
            content.classList.add('-translate-x-full');
            setTimeout(() => {
                menu.classList.add('hidden');
            }, 300);
        }
    }

    function setSearchValue(value) {
        const searchInput = document.getElementById('search-q');
        searchInput.value = value;
        
        // Petit effet visuel lors du clic sur une suggestion
        const container = document.getElementById('search-input-container');
        container.classList.add('ring-4', 'ring-white/30');
        setTimeout(() => {
            container.classList.remove('ring-4', 'ring-white/30');
            document.getElementById('search-form').submit();
        }, 2000);
    }

    function focusSearch() {
        const searchInput = document.getElementById('search-q');
        const container = document.getElementById('search-input-container');
        const form = document.getElementById('search-form');

        // Défilement vers le haut (Hero section)
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        // Petit délai pour attendre le début du défilement
        setTimeout(() => {
            searchInput.focus();
            
            // Animation d'accentuation
            container.classList.add('border-indigo-500', 'ring-4', 'ring-indigo-100');
            form.classList.add('scale-[1.02]');
            
            // Retrait de l'animation après 2 secondes
            setTimeout(() => {
                container.classList.remove('border-indigo-500', 'ring-4', 'ring-indigo-100');
                form.classList.remove('scale-[1.02]');
            }, 2000);
        }, 500);
    }

    // Initialize Tom Select
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof TomSelect !== 'undefined') {
            const config = {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                allowEmptyOption: true,
            };

            const kindSelectElement = document.getElementById("select-kind");
            const categorySelectElement = document.getElementById("select-category");
            const citySelectElement = document.getElementById("select-city");

            if (kindSelectElement && categorySelectElement && citySelectElement) {
                const kindSelect = new TomSelect("#select-kind", config);
                const categorySelect = new TomSelect("#select-category", config);
                new TomSelect("#select-city", config);

                // Store all category options
                const allCategoryOptions = Array.from(document.querySelectorAll('#select-category option')).map(opt => ({
                    value: opt.value,
                    text: opt.text,
                    kind: opt.dataset.kind
                }));

                function filterCategories() {
                    const kind = kindSelect.getValue();
                    if (!kind) {
                        categorySelect.clearOptions();
                        categorySelect.addOptions(allCategoryOptions);
                        return;
                    }
                    
                    const filtered = allCategoryOptions.filter(opt => !opt.kind || opt.kind === kind);
                    categorySelect.clearOptions();
                    categorySelect.addOptions(filtered);
                }

                kindSelect.on('change', filterCategories);
                
                // Initial filter if kind is selected
                if (kindSelect.getValue()) {
                    filterCategories();
                }
            }
        }
    });

    function openBookingModal(id, name) {
        @if(!auth()->check())
        window.location.href = "{{ route('login') }}";
        return;
        @endif
        
        document.getElementById('modalProviderName').innerText = name;
        document.getElementById('bookingForm').action = "/reserver/" + id;
        document.getElementById('bookingModal').classList.remove('hidden');
        document.getElementById('bookingModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeBookingModal() {
        document.getElementById('bookingModal').classList.add('hidden');
        document.getElementById('bookingModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // Close on click outside
    window.onclick = function(event) {
        let modal = document.getElementById('bookingModal');
        if (event.target == modal) {
            closeBookingModal();
        }
    }
</script>

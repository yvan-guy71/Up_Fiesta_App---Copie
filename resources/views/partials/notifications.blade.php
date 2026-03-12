@if(auth()->check())
    <div class="relative">
        <button type="button" 
                id="notification-button" 
                onclick="toggleNotificationsDropdown(event)" 
                class="relative focus:outline-none flex items-center justify-center w-9 h-9 text-slate-500 hover:bg-slate-100 rounded-lg transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z" />
            </svg>
            @if(auth()->user()->unreadNotifications->count())
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full"></span>
            @endif
        </button>
        <div id="notification-dropdown"
             class="fixed sm:absolute right-2 sm:right-0 mt-2 w-72 sm:w-80 max-h-96 overflow-auto bg-white shadow-lg rounded-xl border border-slate-100 z-50 hidden max-w-[calc(100vw-2rem)]">
            <div class="py-2">
                <div class="px-3 sm:px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-50 mb-1">
                    Notifications
                </div>
                @php
                    $notifications = auth()->user()->notifications()->latest()->take(10)->get();
                @endphp
                
                @if($notifications->isEmpty())
                    <div class="px-3 sm:px-4 py-6 text-center">
                        <p class="text-sm text-slate-500">Aucune notification pour le moment.</p>
                    </div>
                @else
                    @foreach($notifications as $notification)
                        <div id="notification-{{ $notification->id }}" class="flex items-start justify-between px-3 sm:px-4 py-3 text-sm hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                            <a href="{{ $notification->data['action_url'] ?? '#' }}" class="flex-1">
                                <p class="text-slate-800">{{ $notification->data['message'] }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </a>
                            <button data-id="{{ $notification->id }}" class="ml-2 text-gray-400 hover:text-gray-600 delete-notif">
                                &times;
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <script>
        if (typeof toggleNotificationsDropdown === 'undefined') {
            window.toggleNotificationsDropdown = function(event) {
                event.stopPropagation();
                const dropdown = document.getElementById('notification-dropdown');
                const settingsDropdown = document.getElementById('settings-dropdown');
                
                // Fermer le menu des paramètres si ouvert
                if (settingsDropdown) settingsDropdown.classList.add('hidden');
                
                dropdown.classList.toggle('hidden');
            };

            // Fermer si clic à l'extérieur
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('notification-dropdown');
                const button = document.getElementById('notification-button');
                
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                }
            });

            // handle delete buttons
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-notif')) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = e.target.getAttribute('data-id');
                    fetch("/notifications/" + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => {
                        if (res.ok) {
                            const row = document.getElementById('notification-' + id);
                            if (row) row.remove();
                        }
                    });
                }
            });
        }
    </script>
@endif

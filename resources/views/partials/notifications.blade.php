@if(auth()->check())
    <div class="relative">
        <button type="button" 
                id="notification-button" 
                onclick="toggleNotificationsDropdown(event)" 
                class="group relative focus:outline-none flex items-center justify-center w-10 h-10 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-indigo-200 dark:hover:border-indigo-800 transition-all duration-200 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-200 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z" />
            </svg>
            @if(auth()->user()->unreadNotifications->count())
                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white dark:ring-slate-800 animate-pulse"></span>
            @endif
        </button>
        <div id="notification-dropdown"
             class="fixed sm:absolute right-2 sm:right-0 mt-3 w-80 sm:w-96 max-h-[35rem] overflow-hidden bg-white dark:bg-slate-900 shadow-2xl shadow-slate-200/60 dark:shadow-black/60 rounded-[1.5rem] border border-slate-200 dark:border-slate-800 z-50 hidden max-w-[calc(100vw-2rem)] flex flex-col ring-1 ring-black/5 dark:ring-white/5">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/50 flex items-center justify-between bg-white dark:bg-slate-800/50 backdrop-blur-md">
                <div class="flex flex-col">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">{{ __('messages.nav.notifications') }}</h3>
                    <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Vous avez :count notifications non lues', ['count' => auth()->user()->unreadNotifications->count()]) }}</p>
                </div>
                @if(auth()->user()->unreadNotifications->count())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </div>
            
            <div class="overflow-y-auto flex-1 bg-white dark:bg-slate-900">
                @php
                    $notifications = auth()->user()->notifications()->latest()->take(10)->get();
                @endphp
                
                @if($notifications->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="relative w-20 h-20 mx-auto mb-6">
                            <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/20 rounded-full animate-pulse"></div>
                            <div class="relative flex items-center justify-center w-full h-full text-indigo-500 dark:text-indigo-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-bold text-slate-400 dark:text-slate-500">{{ __('messages.nav.empty_notifications') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach($notifications as $notification)
                            <div id="notification-{{ $notification->id }}" class="group relative flex items-start gap-4 px-6 py-5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-all duration-200 cursor-pointer">
                                <div class="relative flex-shrink-0 mt-1">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    @if(!$notification->read_at)
                                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-indigo-500 border-2 border-white dark:border-slate-900 rounded-full shadow-[0_0_10px_rgba(99,102,241,0.5)]"></span>
                                    @endif
                                </div>
                                <a href="{{ $notification->data['action_url'] ?? '#' }}" class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-tighter">{{ $notification->data['type_label'] ?? 'Notification' }}</p>
                                        <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 leading-relaxed group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">{{ $notification->data['message'] ?? 'Nouvelle notification' }}</p>
                                </a>
                                <button data-id="{{ $notification->id }}" class="opacity-0 group-hover:opacity-100 w-7 h-7 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 text-slate-400 dark:text-slate-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 hover:text-rose-500 dark:hover:text-rose-400 transition-all duration-200 delete-notif shadow-sm border border-slate-100 dark:border-slate-700" title="{{ __('messages.nav.delete') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            @if(!$notifications->isEmpty())
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800/50">
                    <a href="#" class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-black uppercase tracking-widest transition-all duration-200 shadow-lg shadow-indigo-200/50 dark:shadow-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('messages.nav.mark_all_read') }}
                    </a>
                </div>
            @endif
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
                const deleteBtn = e.target.closest('.delete-notif');
                if (deleteBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = deleteBtn.getAttribute('data-id');
                    fetch("/notifications/" + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => {
                        if (res.ok) {
                            const row = document.getElementById('notification-' + id);
                            if (row) {
                                row.classList.add('opacity-0', 'scale-95');
                                setTimeout(() => row.remove(), 200);
                            }
                        }
                    });
                }
            });
        }
    </script>
@endif

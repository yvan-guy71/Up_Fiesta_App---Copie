@if(auth()->check())
    <div class="relative">
        <button type="button" 
                id="notification-button" 
                onclick="toggleNotificationsDropdown(event)" 
                class="relative focus:outline-none flex items-center justify-center w-9 h-9 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-700 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z" />
            </svg>
            @if(auth()->user()->unreadNotifications->count())
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white dark:ring-slate-800"></span>
            @endif
        </button>
        <div id="notification-dropdown"
             class="fixed sm:absolute right-2 sm:right-0 mt-3 w-80 sm:w-96 max-h-[32rem] overflow-hidden bg-white dark:bg-slate-900 shadow-xl shadow-slate-200 dark:shadow-slate-950/80 rounded-[2rem] border border-slate-100 dark:border-slate-800 z-50 hidden max-w-[calc(100vw-2rem)] flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900/50">
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ __('messages.nav.notifications') }}</h3>
                @if(auth()->user()->unreadNotifications->count())
                    <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-black rounded-full uppercase tracking-tighter">
                        {{ __('messages.nav.new_notifications', ['count' => auth()->user()->unreadNotifications->count()]) }}
                    </span>
                @endif
            </div>
            
            <div class="overflow-y-auto flex-1 bg-white dark:bg-slate-900">
                @php
                    $notifications = auth()->user()->notifications()->latest()->take(10)->get();
                @endphp
                
                @if($notifications->isEmpty())
                    <div class="px-6 py-12 text-center bg-white dark:bg-slate-900">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 dark:text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400 dark:text-slate-500">{{ __('messages.nav.empty_notifications') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
                        @foreach($notifications as $notification)
                            <div id="notification-{{ $notification->id }}" class="group relative flex items-start gap-4 px-6 py-5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                                <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0 {{ $notification->read_at ? 'bg-transparent' : 'bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]' }}"></div>
                                <a href="{{ $notification->data['action_url'] ?? '#' }}" class="flex-1">
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $notification->data['message'] }}</p>
                                    <p class="text-[11px] font-medium text-slate-400 dark:text-slate-500 mt-1.5 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </a>
                                <button data-id="{{ $notification->id }}" class="opacity-0 group-hover:opacity-100 w-8 h-8 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 text-slate-400 hover:text-rose-500 hover:shadow-md transition-all delete-notif shadow-sm border border-slate-100 dark:border-slate-700" title="{{ __('messages.nav.delete') }}">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            @if(!$notifications->isEmpty())
                <div class="px-6 py-4 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 text-center">
                    <a href="#" class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest hover:text-indigo-700 transition-colors">{{ __('messages.nav.mark_all_read') }}</a>
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

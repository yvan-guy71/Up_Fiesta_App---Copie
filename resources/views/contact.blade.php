<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('contact.title') }} - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { scroll-behavior: smooth; }
        html.dark body { background-color: #020617; color: #e5e7eb; }
        html.dark header { background-color: #1a1f2e; border-color: #2d3748; }
        html.dark .bg-white { background-color: #1a1f2e; border-color: #2d3748; }
        html.dark .bg-slate-50 { background-color: #111827; }
        html.dark .text-slate-500, html.dark .text-slate-600, html.dark .text-slate-700, html.dark .text-slate-900 { color: #e5e7eb; }
        html.dark input, html.dark textarea { background-color: #111827; color: #e5e7eb; }
        html.dark input::placeholder, html.dark textarea::placeholder { color: #6b7280; }
        html.dark input:focus, html.dark textarea:focus { background-color: #1a1f2e; }
        html.dark footer { background-color: #1a1f2e; border-color: #2d3748; }
        html.dark .bg-emerald-50 { background-color: #064e3b; }
        html.dark .text-emerald-600 { color: #6ee7b7; }
        html.dark .border-emerald-100 { border-color: #10b981; }
        * { transition-property: background-color, border-color, color; transition-duration: 200ms; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <!-- Header/Nav simplified -->
    @include('partials.header')

    <main class="max-w-4xl mx-auto py-16 px-4 bg-slate-50 dark:bg-slate-950 dark:text-white">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-4">{{ __('contact.title') }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-lg">{{ __('contact.subtitle') }}</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl dark:shadow-2xl shadow-slate-200 dark:shadow-slate-900 border border-slate-100 dark:border-slate-700 overflow-hidden grid md:grid-cols-5">
            <!-- Contact Info Sidebar -->
            <div class="md:col-span-2 bg-indigo-600 p-10 text-white">
                <h3 class="text-2xl font-bold mb-8">{{ __('contact.info_title') }}</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <p class="font-bold">{{ __('contact.address_label') }}</p>
                            <p class="text-indigo-100 text-sm">Lomé, Togo</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="font-bold">{{ __('contact.email_label') }}</p>
                            <p class="text-indigo-100 text-sm">Upfiesta.proj@gmail.com</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <p class="font-bold">{{ __('contact.phone_label') }}</p>
                            <p class="text-indigo-100 text-sm">+228 99 46 25 51</p>
                        </div>
                    </div>
                </div>

                <div class="mt-16">
                    <h4 class="font-bold mb-4">{{ __('contact.follow') }}</h4>
                    <div class="flex gap-4">
                        <a href="https://facebook.com/upfiesta" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-indigo-600 transition-colors group" title="Facebook">
                            <svg class="h-5 w-5 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="https://tiktok.com/@upfiesta" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-black transition-colors group" title="TikTok">
                            <svg class="h-5 w-5 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                        </a>
                        <a href="https://instagram.com/upfiesta" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-rose-600 transition-colors group" title="Instagram">
                            <svg class="h-5 w-5 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.332 3.608 1.308.975.975 1.245 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.332 2.633-1.308 3.608-.975.975-2.242 1.245-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.332-3.608-1.308-.975-.975-1.245-2.242-1.308-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.332-2.633 1.308-3.608.975-.975 2.242-1.245 3.608-1.308 1.266-.058 1.646-.07 4.85-.07zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.272 2.69.072 7.053.014 8.333 0 8.741 0 12s.014 3.667.072 4.947c.2 4.353 2.62 6.777 6.981 6.977 1.28.057 1.688.071 4.947.071s3.667-.014 4.947-.072c4.351-.2 6.777-2.62 6.977-6.981.057-1.28.071-1.688.071-4.947s-.014-3.667-.072-4.947c-.2-4.351-2.62-6.777-6.981-6.977C15.667.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z"/>
                            </svg>
                        </a>
                        <a href="https://linkedin.com/company/upfiesta" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-blue-600 transition-colors group" title="LinkedIn">
                            <svg class="h-5 w-5 fill-current text-white" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="md:col-span-3 p-10">
                @if(session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-500/50 text-emerald-600 dark:text-emerald-300 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="font-bold">{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('contact.form_name') }}</label>
                            <input type="text" id="name" name="name" required value="{{ old('name') }}" class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-700 border-none dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="Yves KODJO">
                            @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('contact.form_email') }}</label>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}" class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-700 border-none dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="email@example.com">
                            @error('email') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('contact.form_subject') }}</label>
                        <input type="text" id="subject" name="subject" required value="{{ old('subject') }}" class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-700 border-none dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="{{ __('contact.form_subject_placeholder') }}">
                        @error('subject') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('contact.form_message') }}</label>
                        <textarea id="message" name="message" required rows="5" class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-700 border-none dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="{{ __('contact.form_message_placeholder') }}">{{ old('message') }}</textarea>
                        @error('message') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white font-black py-4 rounded-2xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 flex items-center justify-center gap-2 group">
                        <span>{{ __('contact.submit') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stored = localStorage.getItem('theme');
            const root = document.documentElement;

            function applyTheme(mode) {
                if (mode === 'dark') {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
            }

            applyTheme(stored === 'dark' ? 'dark' : 'light');

            window.toggleTheme = function() {
                const current = root.classList.contains('dark') ? 'dark' : 'light';
                const next = current === 'dark' ? 'light' : 'dark';
                localStorage.setItem('theme', next);
                applyTheme(next);
            };
        });
    </script>
</body>
</html>

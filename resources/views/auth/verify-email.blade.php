<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification de l'email - Up Fiesta</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="/favicon-192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#004aad">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-900 dark:text-white flex items-center justify-center h-screen">
    <div class="max-w-md w-full space-y-6 bg-white dark:bg-slate-800 p-10 rounded-3xl shadow-lg shadow-slate-200 dark:shadow-black/30 border border-slate-100 dark:border-slate-700">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Vérifiez votre adresse e-mail</h1>
        <p class="text-slate-600 dark:text-slate-400">
            Un lien de vérification a été envoyé à votre adresse. Cliquez dessus pour activer votre compte.
        </p>
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        <form method="POST" action="{{ route('verification.resend') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 rounded-2xl shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 transition-all">
                Renvoyer un email de vérification
            </button>
        </form>
        <p class="text-center text-sm mt-4">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline">Se déconnecter</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </p>
    </div>
</body>
</html>
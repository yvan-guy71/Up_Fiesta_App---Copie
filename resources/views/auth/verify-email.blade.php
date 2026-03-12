<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification de l'email</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900 flex items-center justify-center h-screen">
    <div class="max-w-md w-full space-y-6 bg-white p-10 rounded-3xl shadow-lg">
        <h1 class="text-2xl font-black text-slate-900">Vérifiez votre adresse e-mail</h1>
        <p class="text-slate-600">
            Un lien de vérification a été envoyé à votre adresse. Cliquez dessus pour activer votre compte.
        </p>
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        <form method="POST" action="{{ route('verification.resend') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-2xl shadow hover:bg-indigo-700 transition-all">
                Renvoyer un email de vérification
            </button>
        </form>
        <p class="text-center text-sm mt-4">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-indigo-600 hover:underline">Se déconnecter</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </p>
    </div>
</body>
</html>
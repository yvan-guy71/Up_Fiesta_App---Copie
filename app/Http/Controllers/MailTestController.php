<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    public function send(Request $request)
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'admin') {
            abort(403);
        }

        try {
            $to = $user->email;
            $from = config('mail.from.address');
            $mailer = config('mail.default');

            Mail::raw("Test d'envoi d'email depuis Upfiesta.\nMailer: {$mailer}\nFrom: {$from}", function ($message) use ($to) {
                $message->to($to)->subject('Test email Upfiesta');
            });

            return back()->with('success', "Email de test envoyé à {$to} via le mailer '{$mailer}'.");
        } catch (\Throwable $e) {
            Log::error('Erreur envoi email de test: ' . $e->getMessage());
            return back()->with('error', 'Échec d\'envoi: ' . $e->getMessage());
        }
    }
}




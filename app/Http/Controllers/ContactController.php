<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        try {
            // Envoi de l'email à l'administration
            Mail::to(config('mail.from.address'))->send(new ContactMessageMail($data));
            
            return back()->with('success', 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.');
        } catch (\Exception $e) {
            // En cas d'erreur de configuration mail, on log l'erreur et on informe l'utilisateur
            \Illuminate\Support\Facades\Log::error('Erreur envoi mail contact: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Désolé, une erreur technique est survenue. Veuillez nous contacter directement par téléphone.');
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationApiController extends Controller
{
    public function submit(Request $request)
    {
        $user = Auth::user();
        $provider = $user->provider;

        if (!$provider) {
            return response()->json(['error' => 'Profil prestataire non trouvé.'], 404);
        }

        $request->validate([
            'cni_front' => 'required|image|max:5120',
            'cni_back' => 'required|image|max:5120',
            'company_doc_front' => 'nullable|image|max:5120',
            'company_doc_back' => 'nullable|image|max:5120',
        ]);

        $data = ['verification_status' => 'pending'];

        if ($request->hasFile('cni_front')) {
            $data['cni_photo_front'] = $request->file('cni_front')->store('verification', 'public');
        }
        if ($request->hasFile('cni_back')) {
            $data['cni_photo_back'] = $request->file('cni_back')->store('verification', 'public');
        }
        if ($request->hasFile('company_doc_front')) {
            $data['company_proof_doc_front'] = $request->file('company_doc_front')->store('verification', 'public');
        }
        if ($request->hasFile('company_doc_back')) {
            $data['company_proof_doc_back'] = $request->file('company_doc_back')->store('verification', 'public');
        }

        $provider->update($data);

        return response()->json([
            'message' => 'Documents de vérification soumis avec succès.',
            'status' => 'pending'
        ]);
    }

    public function status()
    {
        $provider = Auth::user()->provider;
        return response()->json([
            'status' => $provider->verification_status ?? 'not_submitted',
            'reason' => $provider->rejection_reason,
        ]);
    }
}

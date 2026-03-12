<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function cgu()
    {
        return view('legal.cgu');
    }

    public function privacy()
    {
        return view('legal.privacy');
    }

    public function legalNotices()
    {
        return view('legal.legal-notices');
    }
}

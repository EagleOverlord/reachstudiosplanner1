<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('account.index', compact('user'));
    }
}


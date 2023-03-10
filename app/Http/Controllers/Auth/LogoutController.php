<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LogoutController extends Controller
{
    public function logout()
    {
        Auth::logout();
        return Inertia::render('login');
    }
}

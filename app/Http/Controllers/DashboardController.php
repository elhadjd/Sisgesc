<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function Dashboard()
    {
        $user = Auth::user();
        $app = DB::table('apps')->where('estado', 'Ativo')->orderBy('app_name', 'ASC')->get();
        $empresa = DB::table('empresas')->orderBy('id', 'ASC')->first();

        return Inertia::render('Dashboard', [
            'Empresa' => $empresa,
            'app' => $app,
            'user' => $user
        ]);
    }
}
<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use App\Models\caixa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class pontoVendaController extends Controller
{
    public function index()
    {
        return Inertia::render('pontoVenda',[
            'data' => caixa::all()
        ]);
    }
}

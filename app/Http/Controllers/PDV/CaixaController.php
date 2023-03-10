<?php

namespace App\Http\Controllers\PDV;

use App\Http\Controllers\Controller;
use App\Models\caixa;
use Illuminate\Http\Request;

class CaixaController extends Controller
{
  public function get()
  {
    return caixa::all();
  }
  public function show(caixa $caixa)
  {
    return $caixa;
  }
}

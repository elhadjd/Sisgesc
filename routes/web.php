<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Compra\compraController;
use App\Http\Controllers\Config\configController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Faturacao\faturacaoController;
use App\Http\Controllers\Funcionarios\funcionarioController;
use App\Http\Controllers\PDV\CaixaController;
use App\Http\Controllers\PDV\pontoVendaController;
use App\Http\Controllers\Stock\stockController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::middleware('license')->group(function () {

    Route::get('/databases', function () {
        return ([['name' => 'test'], ['name' => 'Loja2'], ['name' => 'Loja3']]);
    });

    Route::prefix('auth')->group(function () {
        Route::get('/login', [LoginController::class, 'index'])->name('login');
        Route::post('/logar', [LoginController::class, 'login'])->name('logar');
        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    });


    Route::middleware('auth')->group(function () {
        Route::get('/', [DashboardController::class, 'Dashboard'])->name('dashboard');

        Route::prefix('PDV')->group(function(){
            Route::controller(pontoVendaController::class)->group(function(){
                Route::get('Home','Index')->name('pontodevenda');
            });
            Route::prefix('caixa')->group(function(){
            Route::controller(CaixaController::class)->group(function(){
                Route::get('buscar','get');
                Route::get('show/{caixa}','get');
            });
        });
        });

        Route::prefix('compra')->group(function(){
            Route::controller(compraController::class)->group(function(){
                Route::get('Home','Index')->name('compra');
            });
        });

        Route::prefix('config')->group(function(){
            Route::controller(configController::class)->group(function(){
                Route::get('Home','Index')->name('configuracoes');
            });
        });


        Route::prefix('Faturacao')->group(function(){
            Route::controller(faturacaoController::class)->group(function(){
                Route::get('Home','Index')->name('faturacao');
            });
        });

        Route::prefix('Stock')->group(function(){
            Route::controller(stockController::class)->group(function(){
                Route::get('Home','Index')->name('stock');
            });
        });

        Route::prefix('Funcionarios')->group(function(){
            Route::controller(funcionarioController::class)->group(function(){
                Route::get('Home','Index')->name('funcionarios');
            });
        });
    });
// });

// require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaginaInicialController;
use App\Http\Controllers\PublicarImagemController;
use App\Http\Controllers\LoginComFacebookController;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider and all of them will
  | be assigned to the "web" middleware group. Make something great!
  |
 */

/* Página Padrão */
Route::get('/', [PaginaInicialController::class, 'carregar_pagina']);

/* Página Inicial */
Route::get('/pagina_inicial', [PaginaInicialController::class, 'carregar_pagina']);

/* Publicar Imagem */
Route::get('/publicar_imagem', [PublicarImagemController::class, 'carregar_pagina']);
Route::post('/publicar_imagem/publicar_imagem', [PublicarImagemController::class, 'publicar_imagem']);
Route::get('/publicar_imagem/mostrar_imagens_publicadas_ajax', [PublicarImagemController::class, 'mostrar_imagens_publicadas_ajax']);

/* Login com Facebook */
Route::get('/login_com_facebook', [LoginComFacebookController::class, 'carregar_pagina']);
Route::get('/login_com_facebook/exemplo_de_funcionalidade_que_necessita_de_autenticacao_ajax', [LoginComFacebookController::class, 'exemplo_de_funcionalidade_que_necessita_de_autenticacao_ajax']);

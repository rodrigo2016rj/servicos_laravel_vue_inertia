<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

final class LoginComFacebookController extends TemplateLayoutController{

  public function carregar_pagina(){
    $valores = $this->valores_do_template_layout();

    /* Obtendo valores da configuração deste sistema */
    $id_do_app = config('services.facebook.id_do_app');

    /* Variável que guarda a mensagem da página começa inicialmente vazia */
    $mensagem = '';

    /* $mostrar_formulario_de_autenticacao a princípio é true */
    $mostrar_formulario_de_autenticacao = true;

    /* Validando valores da configuração deste sistema */
    if($id_do_app === null){
      $mensagem = 'Está faltando a configuração FACEBOOK_ID_DO_APP no arquivo .env deste sistema.';
      $mensagem .= ' Informe o ocorrido para os responsáveis deste sistema.';
      $mostrar_formulario_de_autenticacao = false;
    }
    if($id_do_app === ''){
      $mensagem = 'Está faltando um valor para a configuração FACEBOOK_ID_DO_APP no arquivo .env';
      $mensagem .= ' deste sistema. Informe o ocorrido para os responsáveis deste sistema.';
      $mostrar_formulario_de_autenticacao = false;
    }

    $valores['login_com_facebook']['mostrar_formulario_de_autenticacao'] = $mostrar_formulario_de_autenticacao;
    $valores['login_com_facebook']['mensagem'] = $mensagem;
    $valores['login_com_facebook']['id_do_app'] = $id_do_app;

    return Inertia::render('login_com_facebook/login_com_facebook', $valores);
  }
}

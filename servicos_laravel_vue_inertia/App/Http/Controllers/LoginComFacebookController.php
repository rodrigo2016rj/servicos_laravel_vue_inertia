<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use GuzzleHttp\Client as ClienteGuzzle;
use GuzzleHttp\Exception\ClientException as ClienteGuzzleException;
use Illuminate\Support\Facades\Log;

final class LoginComFacebookController extends TemplateLayoutController{

  public function carregar_pagina(){
    $valores = $this->valores_do_template_layout();

    /* Obtendo valores da configuração deste sistema */
    $id_do_app = config('services.facebook.id_do_app');
    $versao_da_api = config('services.facebook.versao_da_api');

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
    if($versao_da_api === null){
      $mensagem = 'Está faltando a configuração FACEBOOK_VERSAO_DA_API no arquivo .env deste sistema.';
      $mensagem .= ' Informe o ocorrido para os responsáveis deste sistema.';
      $mostrar_formulario_de_autenticacao = false;
    }
    if($versao_da_api === ''){
      $mensagem = 'Está faltando um valor para a configuração FACEBOOK_VERSAO_DA_API no arquivo .env';
      $mensagem .= ' deste sistema. Informe o ocorrido para os responsáveis deste sistema.';
      $mostrar_formulario_de_autenticacao = false;
    }

    $valores['login_com_facebook']['mostrar_formulario_de_autenticacao'] = $mostrar_formulario_de_autenticacao;
    $valores['login_com_facebook']['mensagem'] = $mensagem;
    $valores['login_com_facebook']['id_do_app'] = $id_do_app;
    $valores['login_com_facebook']['versao_da_api'] = $versao_da_api;

    return Inertia::render('login_com_facebook/login_com_facebook', $valores);
  }

  public function exemplo_de_funcionalidade_que_necessita_de_autenticacao_ajax(){
    /* Obtendo valores da configuração deste sistema */
    $id_do_app = config('services.facebook.id_do_app');
    $versao_da_api = config('services.facebook.versao_da_api');

    /* Validando valores da configuração deste sistema */
    if($id_do_app === null){
      $aviso = 'A verificação não foi realizada.';
      $aviso .= ' Está faltando a configuração FACEBOOK_ID_DO_APP no arquivo .env deste sistema.';
      $aviso .= ' Informe o ocorrido para os responsáveis deste sistema.';
      $retorno['aviso'] = $aviso;
      $retorno['largura_do_aviso'] = 550;
      echo(json_encode($retorno));
      die;
    }
    if($id_do_app === ''){
      $aviso = 'A verificação não foi realizada.';
      $aviso .= ' Está faltando um valor para a configuração FACEBOOK_ID_DO_APP no arquivo .env';
      $aviso .= ' deste sistema. Informe o ocorrido para os responsáveis deste sistema.';
      $retorno['aviso'] = $aviso;
      $retorno['largura_do_aviso'] = 580;
      echo(json_encode($retorno));
      die;
    }
    if($versao_da_api === null){
      $aviso = 'A verificação não foi realizada.';
      $aviso .= ' Está faltando a configuração FACEBOOK_VERSAO_DA_API no arquivo .env deste sistema.';
      $aviso .= ' Informe o ocorrido para os responsáveis deste sistema.';
      $retorno['aviso'] = $aviso;
      $retorno['largura_do_aviso'] = 560;
      echo(json_encode($retorno));
      die;
    }
    if($versao_da_api === ''){
      $aviso = 'A verificação não foi realizada.';
      $aviso .= ' Está faltando um valor para a configuração FACEBOOK_VERSAO_DA_API no arquivo .env';
      $aviso .= ' deste sistema. Informe o ocorrido para os responsáveis deste sistema.';
      $retorno['aviso'] = $aviso;
      $retorno['largura_do_aviso'] = 580;
      echo(json_encode($retorno));
      die;
    }

    /* Obtendo valores do formulário */
    $requisicao = $this->get_requisicao();
    $token_de_acesso = $requisicao->get('token_de_acesso');
    $id_do_usuario = $requisicao->get('id_do_usuario');

    /* Validando valores do formulário */
    if($id_do_usuario === '' or $id_do_usuario === null){
      $aviso = 'A verificação não foi realizada.';
      $aviso .= ' O id do usuário autenticado precisa estar presente na requisição.';
      $retorno['aviso'] = $aviso;
      $retorno['largura_do_aviso'] = 470;
      echo(json_encode($retorno));
      die;
    }
    if(!is_numeric($id_do_usuario) or $id_do_usuario <= 0 or floor($id_do_usuario) != $id_do_usuario){
      $aviso = 'A verificação não foi realizada.';
      $aviso .= ' O id do usuário autenticado precisa ser um número natural maior que zero.';
      $retorno['aviso'] = $aviso;
      $retorno['largura_do_aviso'] = 350;
      echo(json_encode($retorno));
      die;
    }

    /* Verificando se o usuário está mesmo conectado ao Facebook */
    $endereco_do_servico = 'https://graph.facebook.com/';
    $endereco_do_servico .= $versao_da_api;
    $endereco_do_servico .= '/me';
    $endereco_do_servico .= "?access_token=$token_de_acesso";

    $parametros = array();

    $cliente_guzzle = new ClienteGuzzle();
    try{
      $resposta_guzzle = $cliente_guzzle->request('GET', $endereco_do_servico, $parametros);
    }catch(ClienteGuzzleException $excecao){
      Log::error($excecao->getMessage());
      $codigo_da_excecao = $excecao->getCode();
      $retorno = array();
      switch($codigo_da_excecao){
        default:
          // Facebook fora do ar ou usuário utilizando mais de uma aba do navegador após logout.
          $aviso = 'Verificação concluída, você não tem acesso, tente se conectar novamente.';
          $aviso .= ' Se o problema persistir, informe aos responsáveis deste sistema e peça para';
          $aviso .= ' eles consultarem o arquivo de log do sistema.';
          $retorno['aviso'] = $aviso;
          $retorno['largura_do_aviso'] = 600;
          break;
      }
      echo(json_encode($retorno));
      die;
    }

    $id_confirmado = json_decode($resposta_guzzle->getBody()->getContents())->id;

    if($id_do_usuario !== $id_confirmado){
      $aviso = 'Verificação concluída, você não tem acesso. Tente após reiniciar seu dispositivo.';
      $retorno['aviso'] = $aviso;
      $retorno['largura_do_aviso'] = 400;
      echo(json_encode($retorno));
      die;
    }

    $aviso = 'Verificação concluída, você tem acesso.';
    $retorno['aviso'] = $aviso;
    $retorno['largura_do_aviso'] = 380;
    echo(json_encode($retorno));
    die;
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\PublicarImagemModel;
use App\Models\Entidades\ImagemDoImgur;
use Illuminate\Validation\Validator;
use GuzzleHttp\Client as ClienteGuzzle;
use GuzzleHttp\Exception\ClientException as ClienteGuzzleException;
use Illuminate\Support\Facades\Log;
use DateTime;
use DateTimeZone;
use Inertia\Inertia;

final class PublicarImagemController extends TemplateLayoutController{
  private const QUANTIDADE_PADRAO_POR_PAGINA = 18;
  private $acao_ajax = false;

  public function carregar_pagina($redirecionar = false){
    if($redirecionar){
      //Redireciona para si mesmo, motivo: limpar a requisição.
      header('Location: /publicar_imagem');
      die;
    }

    $valores = $this->valores_do_template_layout();
    $sessao = session();

    /* Variável que guarda a mensagem da página começa inicialmente vazia */
    $mensagem = '';

    /* Mostrando imagens publicadas */
    $valores['publicar_imagem']['lista_de_imagens_publicadas'] = $this->mostrar_imagens_publicadas();

    /* Se houver mensagem na sessão, deve ser mostrada */
    if($sessao->has('mensagem_da_pagina_publicar_imagem')){
      $mensagem = $sessao->get('mensagem_da_pagina_publicar_imagem');
      $sessao->forget('mensagem_da_pagina_publicar_imagem');
      $sessao->save();
    }

    $valores['publicar_imagem']['mensagem'] = $mensagem;
    return Inertia::render('publicar_imagem/publicar_imagem', $valores);
  }

  public function publicar_imagem(){
    $sessao = session();
    $publicar_imagem_model = new PublicarImagemModel();
    $imagem_do_imgur = new ImagemDoImgur();

    /* Obtendo valores da configuração deste sistema */
    $id_do_cliente = config('services.imgur.client_id');

    /* Validando valores da configuração deste sistema */
    if($id_do_cliente === null){
      $mensagem = 'A imagem não foi publicada.';
      $mensagem .= ' Está faltando a configuração IMGUR_CLIENT_ID no arquivo .env deste sistema.';
      $mensagem .= ' Informe o ocorrido para os responsáveis deste sistema.';
      $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
      $sessao->save();
      $this->carregar_pagina(true);
      die;
    }
    if($id_do_cliente === ''){
      $mensagem = 'A imagem não foi publicada.';
      $mensagem .= ' Está faltando um valor para a configuração IMGUR_CLIENT_ID no arquivo .env';
      $mensagem .= ' deste sistema. Informe o ocorrido para os responsáveis deste sistema.';
      $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
      $sessao->save();
      $this->carregar_pagina(true);
      die;
    }

    /* Obtendo valores do formulário */
    $requisicao = $this->get_requisicao();
    $imagem = $requisicao->file('imagem');

    /* Validando o arquivo enviado pelo usuário */
    if($imagem === null){
      $mensagem = 'A imagem não foi publicada.';
      $mensagem .= ' Você precisa selecionar uma imagem antes de tentar publicar.';
      $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
      $sessao->save();
      $this->carregar_pagina(true);
      die;
    }
    if(!$imagem->isValid()){
      $mensagem = 'A imagem não foi publicada.';
      $mensagem .= ' A imagem precisa ser um arquivo válido.';
      $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
      $sessao->save();
      $this->carregar_pagina(true);
      die;
    }
    $validador = new Validator($this->obter_tradutor(), [$imagem], ['image']);
    if(!$validador->passes()){
      $mensagem = 'A imagem não foi publicada.';
      $mensagem .= ' O arquivo que você selecionou não foi considerado como sendo uma imagem.';
      $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
      $sessao->save();
      $this->carregar_pagina(true);
      die;
    }
    unset($validador);

    /* Separando informações da imagem */
    $nome_da_imagem = $imagem->getClientOriginalName();
    $caminho_da_imagem = $imagem->getPathName();

    /* Validando o nome do arquivo enviado pelo usuário */
    $minimo = $imagem_do_imgur->quantidade_minima_de_caracteres('nome');
    $maximo = $imagem_do_imgur->quantidade_maxima_de_caracteres('nome');
    $quantidade = mb_strlen($nome_da_imagem);
    if($quantidade < $minimo){
      $mensagem = 'A imagem não foi publicada.';
      $mensagem .= " O nome da imagem precisa ter no mínimo $minimo caracteres.";
      $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
      $sessao->save();
      $this->carregar_pagina(true);
      die;
    }
    if($quantidade > $maximo){
      $mensagem = 'A imagem não foi publicada.';
      $mensagem .= " O nome da imagem não pode ultrapassar $maximo caracteres.";
      $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
      $sessao->save();
      $this->carregar_pagina(true);
      die;
    }
    $imagem_do_imgur->set_nome($nome_da_imagem);

    /* Fazendo upload da imagem para o site do Imgur (publicando imagem) */
    $endereco_do_servico = 'https://api.imgur.com/3/image';

    $parametros = array();
    $parametros['headers']['authorization'] = "Client-ID $id_do_cliente";
    $parametros['headers']['content-type'] = 'application/x-www-form-urlencoded';
    $parametros['form_params']['image'] = base64_encode(file_get_contents($imagem->path($caminho_da_imagem)));

    $cliente_guzzle = new ClienteGuzzle();
    try{
      $resposta_guzzle = $cliente_guzzle->request('POST', $endereco_do_servico, $parametros);
    }catch(ClienteGuzzleException $excecao){
      $codigo_da_excecao = $excecao->getCode();
      switch($codigo_da_excecao){
        case '403':
          $mensagem = 'A imagem não foi publicada.';
          $mensagem .= ' O sistema do Imgur indica que este sistema não tem permissão para publicar.';
          $mensagem .= ' Informe o ocorrido para os responsáveis deste sistema e peça para';
          $mensagem .= ' verificarem se o valor da configuração IMGUR_CLIENT_ID está correto.';
          $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
          $sessao->save();
          $this->carregar_pagina(true);
          die;
        default:
          Log::error($excecao->getMessage());
          $mensagem = 'A imagem não foi publicada.';
          $mensagem .= ' Um erro inesperado ocorreu.';
          $mensagem .= ' Informe o ocorrido para os responsáveis deste sistema e peça para';
          $mensagem .= ' verificarem o arquivo de log.';
          $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
          $sessao->save();
          $this->carregar_pagina(true);
          die;
      }
    }

    /* Obtendo endereço da imagem publicada */
    $endereco_da_imagem = data_get(response()->json(json_decode(($resposta_guzzle->getBody()->getContents())))->getData(), 'data.link');
    $imagem_do_imgur->set_endereco($endereco_da_imagem);

    /* Momento atual sem fuso horário, pois no banco de dados armazeno sem fuso horário (timezone) */
    $sem_fuso_horario = new DateTimeZone('GMT');
    $objeto_date_time = new DateTime('now', $sem_fuso_horario);
    $momento_atual = $objeto_date_time->format('Y-m-d H:i:s');
    $imagem_do_imgur->set_momento_do_cadastro($momento_atual);

    /* Cadastrando imagem publicada no banco de dados */
    $array_resultado = $publicar_imagem_model->cadastrar_imagem_do_imgur($imagem_do_imgur);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = "A imagem não foi cadastrada, mas foi publicada em $endereco_da_imagem.";
      $mensagem .= ' '.$array_resultado['mensagem_do_model'];
      $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
      $sessao->save();
      $this->carregar_pagina(true);
      die;
    }else{
      $mensagem = 'A imagem foi publicada com sucesso.';
      $sessao->put('mensagem_da_pagina_publicar_imagem', $mensagem);
      $sessao->save();
      $this->carregar_pagina(true);
      die;
    }
  }

  private function mostrar_imagens_publicadas(){
    $publicar_imagem_model = new PublicarImagemModel();
    $valores = array();
    $requisicao = $this->get_requisicao();

    /* Preparando a paginação */
    $pagina = $requisicao->get('pagina');
    if($pagina !== null and (!is_numeric($pagina) or $pagina <= 0 or floor($pagina) != $pagina)){
      header('Location: /publicar_imagem');
      die;
    }

    $quantidade_por_pagina = self::QUANTIDADE_PADRAO_POR_PAGINA;
    $ultima_pagina = $this->calcular_quantidade_de_paginas_da_lista($quantidade_por_pagina);
    if($ultima_pagina != 0 and $pagina > $ultima_pagina){
      if($this->acao_ajax){
        $pagina = $ultima_pagina;
      }else{
        header("Location: /publicar_imagem?pagina=$ultima_pagina");
        die;
      }
    }
    if($ultima_pagina == 0 and $pagina !== null){
      header('Location: /publicar_imagem');
      die;
    }

    $pagina = (int) $pagina;
    $ultima_pagina = (int) $ultima_pagina;

    if($ultima_pagina > 0 and $pagina === 0){
      $pagina = 1;
    }

    $valores['pagina_atual'] = $pagina;
    $valores['ultima_pagina'] = $ultima_pagina;
    $valores['quantidade_por_pagina'] = $quantidade_por_pagina;

    $descartar = $quantidade_por_pagina * $pagina - $quantidade_por_pagina;
    $descartar = max($descartar, 0);

    /* Preparando o resultado */
    $imagens = $publicar_imagem_model->selecionar_imagens_do_imgur($quantidade_por_pagina, $descartar);
    $array_imagens = array();
    foreach($imagens as $imagem){
      $array_imagem = array();

      $array_imagem['id_da_imagem'] = $imagem->get_pk_imagem_do_imgur();
      $array_imagem['nome'] = $imagem->get_nome();
      $array_imagem['endereco'] = $imagem->get_endereco();

      $array_imagens[] = $array_imagem;
    }

    $valores['imagens'] = $array_imagens;

    return $valores;
  }

  private function calcular_quantidade_de_paginas_da_lista($quantidade_por_pagina){
    $publicar_imagem_model = new PublicarImagemModel();

    $array_resultado = $publicar_imagem_model->contar_imagens_do_imgur();
    $quantidade_de_paginas = ceil($array_resultado['quantidade'] / $quantidade_por_pagina);

    return $quantidade_de_paginas;
  }

  public function mostrar_imagens_publicadas_ajax(){
    $this->acao_ajax = true;
    $retorno['lista_de_imagens_publicadas'] = $this->mostrar_imagens_publicadas();
    echo json_encode($retorno);
    die;
  }
}

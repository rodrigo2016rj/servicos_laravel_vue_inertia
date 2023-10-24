<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\Entidades\ImagemDoImgur;
use PDOException;

final class PublicarImagemModel{

  public function cadastrar_imagem_do_imgur($imagem_do_imgur){
    $insert['nome'] = $imagem_do_imgur->get_nome();
    $insert['endereco'] = $imagem_do_imgur->get_endereco();
    $insert['momento_do_cadastro'] = $imagem_do_imgur->get_momento_do_cadastro();

    $array_resultado = array();

    try{
      DB::table('imagem_do_imgur')->insert($insert);
      $array_resultado['id_da_imagem_do_imgur'] = DB::getPdo()->lastInsertId();
    }catch(PDOException $excecao){
      $codigo_da_excecao = $excecao->errorInfo[1];
      switch($codigo_da_excecao){
        default:
          $array_resultado['mensagem_do_model'] = $excecao->getMessage();
          break;
      }
    }

    return $array_resultado;
  }

  public function selecionar_imagens_do_imgur($quantidade, $descartar){
    $query = DB::table('imagem_do_imgur');
    $query = $query->addSelect('pk_imagem_do_imgur');
    $query = $query->addSelect('nome');
    $query = $query->addSelect('endereco');

    $query = $query->orderBy('pk_imagem_do_imgur', 'DESC');

    $query = $query->offset($descartar);
    $query = $query->limit($quantidade);

    $colecao = $query->get();
    $array_resultado = $colecao->all();

    $array_melhorado = array();
    foreach($array_resultado as $objeto_generico){
      $valores = (array) $objeto_generico;
      $imagem_do_imgur = new ImagemDoImgur($valores);
      $array_melhorado[] = $imagem_do_imgur;
    }
    $array_resultado = $array_melhorado;

    return $array_resultado;
  }

  public function contar_imagens_do_imgur(){
    $query = DB::table('imagem_do_imgur');
    $query = $query->select(DB::raw('COUNT(*) AS quantidade'));

    $colecao = $query->get();
    $array_resultado = $colecao->all();

    $array_melhorado['quantidade'] = $array_resultado[0]->quantidade;
    $array_resultado = $array_melhorado;

    return $array_resultado;
  }

}

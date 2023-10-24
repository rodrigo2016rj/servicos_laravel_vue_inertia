<?php

namespace App\Models\Entidades;

final class ImagemDoImgur{
  private $pk_imagem_do_imgur;
  private $nome;
  private $endereco;
  private $momento_do_cadastro;

  public function __construct($array_imagem_do_imgur = array()){
    if(isset($array_imagem_do_imgur['pk_imagem_do_imgur'])){
      $this->pk_imagem_do_imgur = $array_imagem_do_imgur['pk_imagem_do_imgur'];
    }
    if(isset($array_imagem_do_imgur['nome'])){
      $this->nome = $array_imagem_do_imgur['nome'];
    }
    if(isset($array_imagem_do_imgur['endereco'])){
      $this->endereco = $array_imagem_do_imgur['endereco'];
    }
    if(isset($array_imagem_do_imgur['momento_do_cadastro'])){
      $this->momento_do_cadastro = $array_imagem_do_imgur['momento_do_cadastro'];
    }
  }

  public function set_pk_imagem_do_imgur($pk_imagem_do_imgur){
    $this->pk_imagem_do_imgur = $pk_imagem_do_imgur;
  }

  public function set_nome($nome){
    $this->nome = $nome;
  }

  public function set_endereco($endereco){
    $this->endereco = $endereco;
  }

  public function set_momento_do_cadastro($momento_do_cadastro){
    $this->momento_do_cadastro = $momento_do_cadastro;
  }

  public function get_pk_imagem_do_imgur(){
    return $this->pk_imagem_do_imgur;
  }

  public function get_nome(){
    return $this->nome;
  }

  public function get_endereco(){
    return $this->endereco;
  }

  public function get_momento_do_cadastro(){
    return $this->momento_do_cadastro;
  }

  public function quantidade_minima_de_caracteres($atributo){
    switch($atributo){
      case 'nome':
        return 3;
    }
    return -1;
  }

  // O método abaixo deve ser sempre igual ou mais restritivo que o banco de dados
  public function quantidade_maxima_de_caracteres($atributo){
    switch($atributo){
      case 'nome':
        return 60;
    }
    return -1;
  }

}

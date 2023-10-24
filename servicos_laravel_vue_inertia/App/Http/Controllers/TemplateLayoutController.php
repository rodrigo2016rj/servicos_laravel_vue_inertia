<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Translation\Translator;

class TemplateLayoutController extends Controller{
  /* Armazena o objeto da requisição. */
  private $requisicao;

  public function __construct(Request $requisicao){
    $this->requisicao = $requisicao;
  }

  protected final function get_requisicao(){
    return $this->requisicao;
  }

  /** ---------------------------------------------------------------------------------------------
    Retorna uma instância da classe Translator */
  protected final function obter_tradutor(){
    return app(Translator::class);
  }

  /** ---------------------------------------------------------------------------------------------
    Retorna valores que podem ser usados em várias páginas deste sistema */
  protected final function valores_do_template_layout(){
    $valores = array();

    $valores['template_layout']['chave_anti_csrf'] = csrf_token();

    return $valores;
  }

  /** ---------------------------------------------------------------------------------------------
    Acrescenta quebras de linha no padrão XHTML. */
  protected function acrescentar_quebras_de_linha_xhtml($texto){
    //Armazena em array todos os padrões de quebra de linha de sistemas operacionais diferentes
    $tipos_de_quebras_de_sistemas_operacionais = array("\r\n", "\r", "\n");
    //Substitui quebras de linha presentes na string por: termina parágrafo </p> começa parágrafo <p>
    $texto_modificado = str_replace($tipos_de_quebras_de_sistemas_operacionais, '</p><p>', $texto);
    //Substitui parágrafo vazio por: quebra de linha <br/>
    $texto_resultante = str_replace('<p></p>', '<br/>', $texto_modificado);
    //Retorna o texto resultante dentro da tag <p></p>
    return "<p>$texto_resultante</p>";
  }

  /** ---------------------------------------------------------------------------------------------
    Converte yyyy-MM-dd para: dd/MM/yyyy */
  protected function converter_para_data_do_html($data){
    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)){
      //Caso não venha no formato certo, retorna a string sem conversão.
      return $data;
    }
    $ano = substr($data, 0, 4);
    $mes = substr($data, 5, 2);
    $dia = substr($data, 8, 2);
    return "$dia/$mes/$ano";
  }

  /** ---------------------------------------------------------------------------------------------
    Converte yyyy-MM-dd xx:yy:zz para: dd/MM/yyyy às xxhyy */
  protected function converter_para_horario_data_do_html($string){
    if(!preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/', $string)){
      //Caso não venha no formato certo, retorna a string sem conversão.
      return $string;
    }
    $ano = substr($string, 0, 4);
    $mes = substr($string, 5, 2);
    $dia = substr($string, 8, 2);
    $horas = substr($string, 11, 2);
    $minutos = substr($string, 14, 2);
    return "$dia/$mes/$ano às ".$horas.'h'.$minutos;
  }

}

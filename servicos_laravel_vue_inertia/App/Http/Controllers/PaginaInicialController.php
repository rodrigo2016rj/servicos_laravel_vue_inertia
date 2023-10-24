<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

final class PaginaInicialController extends TemplateLayoutController{

  public function carregar_pagina(){
    $valores = $this->valores_do_template_layout();

    return Inertia::render('pagina_inicial/pagina_inicial', $valores);
  }

}

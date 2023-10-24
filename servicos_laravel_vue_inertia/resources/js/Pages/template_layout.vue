<script>
import {Head} from "@inertiajs/vue3";

export default{
  props: [
    "template_layout"
  ],
  components: {
    Head
  },
  data(){
    return{
      /* Propriedades obtidas dos controllers precisam ser recolocadas para prevenir o cache do inertia */
      vue_template_layout: this.template_layout
    }
  },
  mounted(){
    const body = document.getElementsByTagName("body")[0];
    const div_app_template = document.getElementById("div_app_template");
    const div_cabecalho_template = document.getElementById("div_cabecalho_template");
    const div_tronco_template = document.getElementById("div_tronco_template");
    
    /* O resize_observer é importante no Vue para detectar quando as larguras e alturas deixarão de ser automáticas. */
    const resize_observer = new ResizeObserver(function(){
      /* Enquanto as larguras e alturas forem automáticas não há como fazer ajustes, por isso retorna. */
      var estilo_computado = window.getComputedStyle(div_app_template);
      if(estilo_computado.height === "auto"){
        return;
      }
      
      /* Ajustando altura do tronco para preencher a parte vertical visível da tela. */
      let altura_minima = window.innerHeight;
      
      var estilo_computado = window.getComputedStyle(body);
      altura_minima -= parseInt(estilo_computado.marginTop, 10);
      altura_minima -= parseInt(estilo_computado.borderTopWidth, 10);
      altura_minima -= parseInt(estilo_computado.paddingTop, 10);
      
      var estilo_computado = window.getComputedStyle(div_cabecalho_template);
      altura_minima -= parseInt(estilo_computado.marginTop, 10);
      altura_minima -= parseInt(estilo_computado.borderTopWidth, 10);
      altura_minima -= parseInt(estilo_computado.paddingTop, 10);
      altura_minima -= parseInt(estilo_computado.height, 10);
      altura_minima -= parseInt(estilo_computado.paddingBottom, 10);
      altura_minima -= parseInt(estilo_computado.borderBottomWidth, 10);
      altura_minima -= parseInt(estilo_computado.marginBottom, 10);
      
      var estilo_computado = window.getComputedStyle(div_tronco_template);
      altura_minima -= parseInt(estilo_computado.marginTop, 10);
      altura_minima -= parseInt(estilo_computado.borderTopWidth, 10);
      altura_minima -= parseInt(estilo_computado.paddingTop, 10);
      altura_minima -= parseInt(estilo_computado.paddingBottom, 10);
      altura_minima -= parseInt(estilo_computado.borderBottomWidth, 10);
      altura_minima -= parseInt(estilo_computado.marginBottom, 10);
      
      div_tronco_template.style.minHeight = altura_minima + "px";
    }.bind(this));
    
    resize_observer.observe(div_app_template);
  }
}
</script>

<template>
  <div id="div_cabecalho_template">
    <div id="div_titulo_do_sistema_template">
      <h1 id="h1_titulo_do_sistema_template">
        <span>Diversos Serviços Web</span>
      </h1>
      <div id="div_tecnologias_do_sistema_no_cabecalho_template">
        <span>Este sistema utiliza PHP, Laravel, Vue e Inertia</span>
      </div>
    </div>
  </div>
  <div id="div_tronco_template">
    <div id="div_menu_lateral_template">
      <div id="div_grupo_01_de_itens_do_menu_template">
        <a id="link_pagina_inicial_template" href="/pagina_inicial">Início</a>
      </div>
      <div id="div_grupo_02_de_itens_do_menu_template">
        <a id="link_publicar_imagem_template" href="/publicar_imagem">Publicar Imagem</a>
      </div>
    </div>
    <slot name="conteudo"></slot>
  </div>
  <div id="div_rodape_template">
    <div id="div_autor_do_sistema_template">
      <span>Este sistema foi feito por Rodrigo Diniz da Silva.</span>
    </div>
    <div id="div_tecnologias_do_sistema_no_rodape_template">
      <span>Este sistema utiliza PHP, Laravel, Vue e Inertia.</span>
    </div>
  </div>
</template>

<style>
  body{
    margin: 0px;
    background: #FFF;
    color: #303030;
    font-family: Verdana, Tahoma, Times New Roman, serif;
    font-size: 12pt;
  }
  
  /* Cabeçalho do sistema: */
  #div_titulo_do_sistema_template{
    padding-top: 16px;
    padding-bottom: 18px;
    background-color: #343458;
    color: #FFF;
    text-align: center;
  }
  @media(max-width: 420px){
    #div_tecnologias_do_sistema_no_cabecalho_template{
      margin-right: auto;
      margin-left: auto;
      min-width: 250px;
      max-width: 250px;
    }
  }
  
  /* Tronco do sistema: */
  #div_tronco_template{
    margin-right: auto;
    margin-left: auto;
    padding: 30px 0px;
    width: 100%;
    max-width: 1920px;
    min-height: 2000px; /* Será modificada pelo script */
    background-color: #FFF;
    overflow: hidden;
  }
  @media(max-width: 880px){
    #div_tronco_template{
      padding: 10px 0px 20px 0px;
    }
  }
  @media(max-width: 640px){
    #div_tronco_template{
      padding: 0px 0px 15px 0px;
    }
  }
  #div_menu_lateral_template{
    float: left;
    padding: 0px 20px;
    min-width: calc(340px - 40px);
    max-width: calc(340px - 40px);
  }
  #div_menu_lateral_template>div{
    margin-top: 10px;
    border: 1px solid #C4C4C4;
    min-width: calc(100% - 2px);
    max-width: calc(100% - 2px);
  }
  #div_menu_lateral_template>div:first-child{
    margin-top: 0px;
  }
  #div_menu_lateral_template>div>a,
  #div_menu_lateral_template>div>a:visited{
    display: block;
    padding: 14px;
    min-width: calc(100% - 28px);
    max-width: calc(100% - 28px);
    color: #404080;
    font-size: 12pt;
    letter-spacing: 1px;
    text-shadow: 1px 0px 0px #404080;
  }
  #div_menu_lateral_template>div>a:hover{
    background-color: #404080;
    color: #FFF;
    text-decoration: none;
    text-shadow: 1px 0px 0px #FFF;
  }
  @media(max-width: 880px){
    #div_menu_lateral_template{
      float: none;
      padding: 0px 40px;
      min-width: calc(100% - 80px);
      max-width: calc(100% - 80px);
    }
  }
  @media(max-width: 640px){
    #div_menu_lateral_template{
      border-top: 2px solid #343458;
      border-bottom: 2px solid #343458;
      border-left: 2px solid #343458;
      padding: 0px;
      min-width: 100%;
      max-width: 100%;
      font-size: 0pt;
      white-space: nowrap;
      overflow: auto;
    }
    #div_menu_lateral_template>div{
      display: inline-block;
      margin-top: 0px;
      border: none;
      border-right: 2px solid #343458;
      padding: 0px;
      min-width: 0px;
      max-width: none;
      vertical-align: top;
      font-size: 0pt;
      white-space: nowrap;
      overflow: auto;
    }
    #div_menu_lateral_template>div>a,
    #div_menu_lateral_template>div>a:visited{
      display: inline-block;
      margin-top: 0px;
      border-left: 2px solid #343458;
      padding: 14px 8px;
      min-width: 0px;
      max-width: none;
      vertical-align: top;
    }
    #div_menu_lateral_template>div>a:first-child{
      border-left: none;
    }
  }
  
  /* Rodapé do sistema: */
  #div_rodape_template{
    padding: 30px 0px;
    background-color: #343458;
    color: #FFF;
  }
  #div_autor_do_sistema_template{
    text-align: center;
  }
  @media(max-width: 450px){
    #div_autor_do_sistema_template{
      margin-right: auto;
      margin-left: auto;
      min-width: 250px;
      max-width: 250px;
    }
  }
  #div_tecnologias_do_sistema_no_rodape_template{
    text-align: center;
  }
  @media(max-width: 450px){
    #div_tecnologias_do_sistema_no_rodape_template{
      margin-top: 20px;
      margin-right: auto;
      margin-left: auto;
      min-width: 250px;
      max-width: 250px;
    }
  }
  
  /* Classes para qualquer tag: */
  .tag_oculta{
    display: none !important;
  }
  
  /* Estilos iniciais: */
  h1, h2, h3, h4, h5, h6{
    margin: 0px;
    font-family: Verdana, Tahoma, Times New Roman, serif;
    font-size: 20pt;
    font-weight: normal;
  }
  textarea{
    border: 1px solid #B0B0C8;
    padding: 4px 5px;
    width: calc(212px - 2px - 10px);
    color: #282828;
    font-family: Verdana, Tahoma, Times New Roman, serif;
    font-size: 12pt;
    appearance: none;
    outline: none;
  }
  textarea:hover,
  textarea:focus{
    border: 1px solid #9090C8;
    appearance: none;
    outline: none;
  }
  input[type="submit"],
  input[type="reset"],
  button{
    border-top: 1px solid #B0B0C8;
    border-right: 2px solid #B0B0C8;
    border-bottom: 2px solid #B0B0C8;
    border-left: 1px solid #B0B0C8;
    border-radius: 4px;
    padding: 4px 4px 4px 3px;
    background-color: #FFF;
    color: #404070;
    font-family: Tahoma, Verdana, Times New Roman, serif;
    font-size: 11pt;
    cursor: pointer;
  }
  input[type="submit"]:hover,
  input[type="reset"]:hover,
  button:hover{
    border-top: 1px solid #9090C8;
    border-right: 2px solid #9090C8;
    border-bottom: 2px solid #9090C8;
    border-left: 1px solid #9090C8;
    background-color: #F0F0FF;
    appearance: none;
    outline: none;
  }
  input[type="submit"]:focus,
  input[type="reset"]:focus,
  button:focus{
    border-top: 2px solid #9090C8;
    border-right: 1px solid #9090C8;
    border-bottom: 1px solid #9090C8;
    border-left: 2px solid #9090C8;
    padding: 5px 3px 3px 4px;
    appearance: none;
    outline: none;
  }
  p{
    margin-top: 0px;
    margin-bottom: 0px;
    text-align: justify;
  }
  a{
    color: #0000B0;
    text-decoration: none;
    cursor: pointer;
  }
  a:visited{
    color: #0000B0;
  }
  a:hover{
    color: #0000B0;
    text-decoration: underline;
  }
</style>

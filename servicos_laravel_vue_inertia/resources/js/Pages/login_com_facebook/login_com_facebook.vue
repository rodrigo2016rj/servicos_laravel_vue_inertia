<script>
  import {Head} from "@inertiajs/vue3";
  import TemplateLayout from "../template_layout.vue";
  
  export default{
    props: [
      "template_layout",
      "login_com_facebook"
    ],
    components: {
      Head,
      TemplateLayout
    },
    data(){
      return{
        /* Propriedades obtidas dos controllers precisam ser recolocadas para prevenir o cache do inertia */
        vue_template_layout: this.template_layout,
        vue_login_com_facebook: this.login_com_facebook,
        
        /* Propriedades novas e seus valores iniciais */
        token_de_acesso: null,
        usuario_esta_autenticado: null,
        id_do_usuario_autenticado_pelo_facebook: null,
        nome_do_usuario_autenticado_pelo_facebook: null,
        
        mostrar_popup_aviso: false,
        aviso: "",
        largura_da_div_aviso: "600px",
        
        rolagem_horizontal_da_janela: 0,
        rolagem_vertical_da_janela: 0
      }
    },
    created(){
      window.fbAsyncInit = function(){
        FB.init({
          appId: this.vue_login_com_facebook.id_do_app,
          cookie: true,
          xfbml: true,
          version: this.vue_login_com_facebook.versao_da_api
        });
        
        FB.getLoginStatus(function(resposta){
          this.token_de_acesso = null;
          this.usuario_esta_autenticado = null;
          this.id_do_usuario_autenticado_pelo_facebook = null;
          this.nome_do_usuario_autenticado_pelo_facebook = null;
          
          if(resposta.status === "connected"){
            this.token_de_acesso = resposta.authResponse.accessToken;
            if(resposta.authResponse){
              axios.get("https://graph.facebook.com/" + this.vue_login_com_facebook.versao_da_api + "/me", {
                params: {
                  access_token: resposta.authResponse.accessToken,
                  method: "get",
                  pretty: "0",
                  sdk: this.vue_login_com_facebook.versao_da_api,
                  suppress_http_code: "1"
                }
              }).then(resposta => {
                if(typeof resposta.data.name != "undefined" && resposta.data.name !== null){
                  this.usuario_esta_autenticado = true;
                  this.id_do_usuario_autenticado_pelo_facebook = resposta.data.id;
                  this.nome_do_usuario_autenticado_pelo_facebook = resposta.data.name;
                }else{
                  this.desconectar_a_conta_do_facebook(false);
                  this.token_de_acesso = null;
                  this.usuario_esta_autenticado = false;
                  this.id_do_usuario_autenticado_pelo_facebook = null;
                  this.nome_do_usuario_autenticado_pelo_facebook = null;
                }
              }).catch(function(excecao){
                this.desconectar_a_conta_do_facebook(false);
                this.token_de_acesso = null;
                this.usuario_esta_autenticado = false;
                this.id_do_usuario_autenticado_pelo_facebook = null;
                this.nome_do_usuario_autenticado_pelo_facebook = null;
              }.bind(this));
              
              // Outro jeito:
              // FB.api("/me", {fields: "name"}, function(resposta){
              //   if(typeof resposta.name != "undefined" && resposta.name !== null){
              //     this.usuario_esta_autenticado = true;
              //     this.id_do_usuario_autenticado_pelo_facebook = resposta.id;
              //     this.nome_do_usuario_autenticado_pelo_facebook = resposta.name;
              //   }else{
              //     this.token_de_acesso = null;
              //     this.usuario_esta_autenticado = false;
              //     this.id_do_usuario_autenticado_pelo_facebook = null;
              //     this.nome_do_usuario_autenticado_pelo_facebook = null;
              //   }
              // }.bind(this));
            }
          }else{
            this.token_de_acesso = null;
            this.usuario_esta_autenticado = false;
            this.id_do_usuario_autenticado_pelo_facebook = null;
            this.nome_do_usuario_autenticado_pelo_facebook = null;
          }
        }.bind(this));
      }.bind(this);
      
      window.addEventListener("click", function(evento){
        let tag_alvo = evento.target;
        
        ciclo:
        while(true){
          if(tag_alvo === null || !tag_alvo.tagName){
            this.remover_popups();
            break;
          }
          
          switch(tag_alvo.id){
            case "div_aviso":
              break ciclo;
            break;
          }
          
          tag_alvo = tag_alvo.parentNode;
        }
      }.bind(this));
      
      window.addEventListener("resize", function(evento){
        this.rolagem_horizontal_da_janela = window.scrollX;
        this.rolagem_vertical_da_janela = window.scrollY;
        if(this.mostrar_popup_aviso === true){
          this.mostrar_popup_aviso = "reposicionar";
        }
      }.bind(this));
      
      window.addEventListener("scroll", function(evento){
        this.rolagem_horizontal_da_janela = window.scrollX;
        this.rolagem_vertical_da_janela = window.scrollY;
      }.bind(this));
    },
    methods: {
      remover_foco_do_botao(evento){
        evento.currentTarget.blur();
      },
      conectar_a_conta_do_facebook(evento){
        evento.currentTarget.blur();
        
        if(this.usuario_esta_autenticado){
          //Reconectar
          FB.logout(function(resposta){
            this.token_de_acesso = null;
            this.usuario_esta_autenticado = false;
            this.id_do_usuario_autenticado_pelo_facebook = null;
            this.nome_do_usuario_autenticado_pelo_facebook = null;
            
            FB.login(function(resposta){
              if(resposta.status === "connected"){
              this.token_de_acesso = resposta.authResponse.accessToken;
                if(resposta.authResponse){
                  axios.get("https://graph.facebook.com/" + this.vue_login_com_facebook.versao_da_api + "/me", {
                    params: {
                      access_token: resposta.authResponse.accessToken,
                      method: "get",
                      pretty: "0",
                      sdk: this.vue_login_com_facebook.versao_da_api,
                      suppress_http_code: "1"
                    }
                  }).then(resposta => {
                    if(typeof resposta.data.name != "undefined" && resposta.data.name !== null){
                      this.usuario_esta_autenticado = true;
                      this.id_do_usuario_autenticado_pelo_facebook = resposta.data.id;
                      this.nome_do_usuario_autenticado_pelo_facebook = resposta.data.name;
                      this.aviso = "Você conectou sua conta do Facebook com sucesso.";
                      this.largura_da_div_aviso = "480px";
                      this.exibir_popup_aviso();
                    }
                  }).catch(function(excecao){
                    this.desconectar_a_conta_do_facebook(false);
                    this.token_de_acesso = null;
                    this.usuario_esta_autenticado = false;
                    this.id_do_usuario_autenticado_pelo_facebook = null;
                    this.nome_do_usuario_autenticado_pelo_facebook = null;
                    this.aviso = "Conecção com Facebook falhou, tente novamente após desativar addons antirrastreio do seu navegador caso tenha algum instalado.";
                    this.largura_da_div_aviso = "480px";
                    this.exibir_popup_aviso();
                  }.bind(this));
                }
              }else{
                this.aviso = "Conecção com Facebook falhou, tente novamente mais tarde.";
                this.largura_da_div_aviso = "560px";
                this.exibir_popup_aviso();
                return;
              }
            }.bind(this));
          }.bind(this));
        }else{
          //Conectar
          FB.login(function(resposta){
            if(resposta.status === "connected"){
              this.token_de_acesso = resposta.authResponse.accessToken;
              if(resposta.authResponse){
                axios.get("https://graph.facebook.com/" + this.vue_login_com_facebook.versao_da_api + "/me", {
                  params: {
                    access_token: resposta.authResponse.accessToken,
                    method: "get",
                    pretty: "0",
                    sdk: this.vue_login_com_facebook.versao_da_api,
                    suppress_http_code: "1"
                  }
                }).then(resposta => {
                  if(typeof resposta.data.name != "undefined" && resposta.data.name !== null){
                    this.usuario_esta_autenticado = true;
                    this.id_do_usuario_autenticado_pelo_facebook = resposta.data.id;
                    this.nome_do_usuario_autenticado_pelo_facebook = resposta.data.name;
                    this.aviso = "Você conectou sua conta do Facebook com sucesso.";
                    this.largura_da_div_aviso = "480px";
                    this.exibir_popup_aviso();
                  }
                }).catch(function(excecao){
                  this.desconectar_a_conta_do_facebook(false);
                  this.token_de_acesso = null;
                  this.usuario_esta_autenticado = false;
                  this.id_do_usuario_autenticado_pelo_facebook = null;
                  this.nome_do_usuario_autenticado_pelo_facebook = null;
                  this.aviso = "Conecção com Facebook falhou, tente novamente após desativar addons antirrastreio do seu navegador caso tenha algum instalado.";
                  this.largura_da_div_aviso = "480px";
                  this.exibir_popup_aviso();
                }.bind(this));
              }
            }else{
              this.aviso = "Conecção com Facebook falhou, tente novamente mais tarde.";
              this.largura_da_div_aviso = "560px";
              this.exibir_popup_aviso();
              return;
            }
          }.bind(this));
        }
      },
      desconectar_a_conta_do_facebook(mostrar_aviso, evento = null){
        if(evento !== null){
          evento.currentTarget.blur();
        }
        
        if(this.usuario_esta_autenticado === false){
          mostrar_aviso = false;
        }
        
        FB.logout(function(resposta){
          this.token_de_acesso = null;
          this.usuario_esta_autenticado = false;
          this.id_do_usuario_autenticado_pelo_facebook = null;
          this.nome_do_usuario_autenticado_pelo_facebook = null;
          
          if(mostrar_aviso){
            this.aviso = "Você desconectou sua conta do Facebook com sucesso.";
            this.largura_da_div_aviso = "500px";
            this.exibir_popup_aviso();
          }
        }.bind(this));
      },
      verificar_acesso(evento){
        evento.currentTarget.blur();
        
        if(this.usuario_esta_autenticado){
          axios.get("login_com_facebook/exemplo_de_funcionalidade_que_necessita_de_autenticacao_ajax", {
            params: {
              token_de_acesso: this.token_de_acesso,
              id_do_usuario: this.id_do_usuario_autenticado_pelo_facebook
            }
          }).then(resposta => {
            this.aviso = resposta.data.aviso;
            this.largura_da_div_aviso = resposta.data.largura_do_aviso + "px";
            this.exibir_popup_aviso();
          });
        }else{
          this.aviso = "Você precisa conectar sua conta do Facebook antes de verificar o acesso.";
          this.largura_da_div_aviso = "500px";
          this.exibir_popup_aviso();
        }
      },
      remover_popups(){
        this.mostrar_popup_aviso = false;
      },
      exibir_popup_aviso(){
        if(this.mostrar_popup_aviso === true){
          this.remover_popups();
          this.mostrar_popup_aviso = "reposicionar";
        }else{
          this.remover_popups();
          this.mostrar_popup_aviso = true;
        }
      }
    },
    watch: {
      mostrar_popup_aviso(valor_atual, valor_anterior){
        if(valor_atual === "reposicionar"){
          /* Aciona o watch novamente */
          this.mostrar_popup_aviso = true;
          return;
        }
        
        const div_aviso = document.getElementById("div_aviso");
        if(valor_atual === true){
          div_aviso.classList.remove("tag_oculta");
          
          let largura_da_div = parseInt(this.largura_da_div_aviso, 10);
          
          const tag_html = document.querySelector("html");
          
          let largura_da_tag_html = 0;
          var estilo_computado = window.getComputedStyle(tag_html);
          largura_da_tag_html += parseInt(estilo_computado.width, 10);
          
          var posicao_x = largura_da_tag_html / 2 - largura_da_div / 2;
          if(window.innerWidth <= largura_da_div){
            posicao_x = 0;
          }
          
          const div_cabecalho_template = document.getElementById("div_cabecalho_template");
          
          let altura_do_cabecalho = 0;
          var estilo_computado = window.getComputedStyle(div_cabecalho_template);
          altura_do_cabecalho += parseInt(estilo_computado.borderTopWidth, 10);
          altura_do_cabecalho += parseInt(estilo_computado.paddingTop, 10);
          altura_do_cabecalho += parseInt(estilo_computado.height, 10);
          altura_do_cabecalho += parseInt(estilo_computado.paddingBottom, 10);
          altura_do_cabecalho += parseInt(estilo_computado.borderBottomWidth, 10);
          
          var acrescimo = 16;
          var posicao_y = altura_do_cabecalho + acrescimo;
          if(this.rolagem_vertical_da_janela > altura_do_cabecalho + acrescimo){
            posicao_y = this.rolagem_vertical_da_janela + acrescimo;
          }
          
          let altura_da_div = 0;
          var estilo_computado = window.getComputedStyle(div_aviso);
          altura_da_div += parseInt(estilo_computado.borderTopWidth, 10);
          altura_da_div += parseInt(estilo_computado.paddingTop, 10);
          altura_da_div += parseInt(estilo_computado.height, 10);
          altura_da_div += parseInt(estilo_computado.paddingBottom, 10);
          altura_da_div += parseInt(estilo_computado.borderBottomWidth, 10);
          
          if(window.innerHeight <= altura_do_cabecalho + altura_da_div + acrescimo * 2){
            posicao_y = this.rolagem_vertical_da_janela + acrescimo;
            if(window.innerHeight <= altura_da_div + acrescimo * 2){
              posicao_y = this.rolagem_vertical_da_janela;
            }
          }
          
          div_aviso.style.position = "absolute";
          div_aviso.style.top = posicao_y + "px";
          div_aviso.style.left = posicao_x + "px";
        }else{
          div_aviso.classList.add("tag_oculta");
        }
      }
    }
  }
</script>

<template>
  <Head>
    <title>Login com Facebook</title>
    <component is="script" async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></component>
  </Head>
  <TemplateLayout :template_layout="vue_template_layout">
    <template #conteudo>
      <div id="div_pagina_do_sistema">
        <h2 id="h2_titulo_da_pagina">
          <span>Login com Facebook</span>
        </h2>
        <div id="div_mensagem" v-if="vue_login_com_facebook.mensagem">
          <span id="span_mensagem">{{vue_login_com_facebook.mensagem}}</span>
        </div>
        <div id="div_autenticacao_com_facebook" v-if="vue_login_com_facebook.mostrar_formulario_de_autenticacao">
          <div id="div_status_da_autenticacao">
            <span id="span_status_autenticado" v-if="usuario_esta_autenticado">
              Sua conta do Facebook está conectada.
              <template v-if="nome_do_usuario_autenticado_pelo_facebook !== null">
                <br/>
                Nome {{nome_do_usuario_autenticado_pelo_facebook}}.
              </template>
            </span>
            <span id="span_status_nao_autenticado" v-else-if="usuario_esta_autenticado === false">
              Sua conta do Facebook não está conectada.
            </span>
            <span id="span_status_analisando_conexao" v-else>
              Analisando conexão...
            </span>
          </div>
          <div id="div_instrucoes_da_autenticacao">
            <span>Conectar/Desconectar conta do Facebook para este sistema.</span>
          </div>
          <div id="div_botoes_da_autenticacao">
            <button id="botao_conectar" type="button" @mouseleave="remover_foco_do_botao" 
                    @click="conectar_a_conta_do_facebook">Conectar</button>
            <button id="botao_desconectar" type="button" @mouseleave="remover_foco_do_botao" 
                    @click="desconectar_a_conta_do_facebook(true, $event)">Desconectar</button>
          </div>
        </div>
        <div id="div_exemplo_de_funcionalidade_que_necessita_de_autenticacao" v-if="token_de_acesso">
          <div id="div_instrucoes_da_funcionalidade">
            <span>
              Clique no botão "Verificar Acesso" para verificar se este sistema reconhece sua conexão com o Facebook.
            </span>
          </div>
          <div id="div_botao_da_funcionalidade">
            <button id="botao_verificar_acesso" type="button" @mouseleave="remover_foco_do_botao" 
                    @click="verificar_acesso">Verificar Acesso</button>
          </div>
        </div>
        <div id="div_aviso" class="tag_oculta">
          <div class="div_fechar" @click="remover_popups">
            <span>X</span>
          </div>
          <div id="div_texto_do_aviso">
            <span id="span_texto_do_aviso">{{aviso}}</span>
          </div>
        </div>
      </div>
    </template>
  </TemplateLayout>
</template>

<style>
  #div_pagina_do_sistema{
    float: left;
    padding-right: 40px;
    padding-left: 40px;
    width: calc(100% - 40px - 40px - 340px * 2);
    max-width: calc(100% - 40px - 40px - 340px);
  }
  @media(max-width: 1520px){
    #div_pagina_do_sistema{
      padding-left: 0px;
      width: calc(100% - 40px - 340px);
      max-width: calc(100% - 40px - 340px);
    }
  }
  @media(max-width: 880px){
    #div_pagina_do_sistema{
      float: none;
      margin-top: 21px;
      padding-right: 40px;
      padding-left: 40px;
      width: calc(100% - 40px - 40px);
      max-width: calc(100% - 40px - 40px);
    }
  }
  @media(max-width: 640px){
    #div_pagina_do_sistema{
      margin-top: 15px;
      padding-right: 20px;
      padding-left: 20px;
      width: calc(100% - 20px - 20px);
      max-width: calc(100% - 20px - 20px);
    }
  }
  #h2_titulo_da_pagina{
    color: #707070;
    font-size: 22pt;
    text-align: center;
  }
  @media(max-width: 640px){
    #h2_titulo_da_pagina{
      font-size: 18pt;
    }
  }
  #div_mensagem{
    margin-top: 30px;
    margin-right: auto;
    margin-left: auto;
    max-width: 500px;
    text-align: center;
  }
  @media(max-width: 880px){
    #div_mensagem{
      margin-top: 20px;
    }
  }
  #div_autenticacao_com_facebook{
    margin-top: 30px;
  }
  #div_status_da_autenticacao{
    margin-right: auto;
    margin-left: auto;
    max-width: 500px;
    text-align: center;
  }
  #span_status_autenticado{
    color: #0000A0;
  }
  #span_status_nao_autenticado{
    color: #A00000;
  }
  #span_status_analisando_conexao{
    color: #000;
  }
  #div_instrucoes_da_autenticacao{
    margin-top: 30px;
    margin-right: auto;
    margin-left: auto;
    max-width: 500px;
    text-align: center;
  }
  @media(max-width: 640px){
    #div_instrucoes_da_autenticacao{
      max-width: 300px;
    }
  }
  #div_botoes_da_autenticacao{
    margin-top: 30px;
    margin-right: auto;
    margin-left: auto;
    max-width: 500px;
    text-align: center;
  }
  #botao_conectar{
    display: inline-block;
    vertical-align: top;
  }
  #botao_desconectar{
    display: inline-block;
    margin-left: 20px;
    vertical-align: top;
  }
  #div_exemplo_de_funcionalidade_que_necessita_de_autenticacao{
    margin-top: 70px;
  }
  #div_instrucoes_da_funcionalidade{
    margin-right: auto;
    margin-left: auto;
    max-width: 580px;
    text-align: justify;
    text-indent: 70px;
  }
  @media(max-width: 640px){
    #div_instrucoes_da_funcionalidade{
      max-width: 350px;
      text-indent: 35px;
    }
  }
  #div_botao_da_funcionalidade{
    margin-top: 30px;
    margin-right: auto;
    margin-left: auto;
    max-width: 500px;
    text-align: center;
  }
  #botao_verificar_acesso{
    display: inline-block;
    vertical-align: top;
  }
  #div_aviso{
    position: absolute;
    top: 0px; /* Será modificado pelo javascript */
    left: 0px; /* Será modificado pelo javascript */
    border: 1px solid #A4A4A4;
    border-radius: 12px;
    padding: 22px 30px 20px 15px;
    width: calc(v-bind(largura_da_div_aviso) - 2px - 30px - 15px);
    max-width: calc(100% - 2px - 30px - 15px);
    background-color: #F8F8F8;
    text-align: center;
    box-shadow: 0px 0px 8px 0px rgba(23, 23, 31, 0.75);
    z-index: 2;
  }
  .div_fechar{
    position: absolute;
    top: 0px;
    right: 0px;
    border-top-right-radius: 8px;
    border-bottom-left-radius: 8px;
    padding: 2px 6px;
    background-color: #C04848;
    color: #FFF;
    font-size: 14pt;
    cursor: pointer;
  }
  .div_fechar:hover{
    background-color: #A01818;
  }
</style>

<script>
  import {Head} from "@inertiajs/vue3";
  import TemplateLayout from "../template_layout.vue";
  
  export default{
    props: [
      "template_layout",
      "publicar_imagem"
    ],
    components: {
      Head,
      TemplateLayout
    },
    data(){
      return{
        /* Propriedades obtidas dos controllers precisam ser recolocadas para prevenir o cache do inertia */
        vue_template_layout: this.template_layout,
        vue_publicar_imagem: this.publicar_imagem,
        
        /* Propriedades novas e seus valores iniciais */
        nome_do_arquivo_que_sera_enviado: "Imagem",
        caminho_do_arquivo_que_sera_enviado: "",
        status_da_lista: "",
        lista_de_paginas: new Array(0),
        quantidade_de_imagens: 0,
        quantidade_de_espacos_vazios: 0,
        contador_ajax: 0
      }
    },
    created(){
      this.criar_lista_de_paginas();
      this.calcula_quantidade_de_imagens_e_de_espacos_vazios();
    },
    methods: {
      criar_lista_de_paginas(){
        const pagina_atual = this.vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual;
        const inicio_da_sequencia = Math.max(pagina_atual - 4, 1);
        const final_da_sequencia = Math.min(pagina_atual + 4, this.vue_publicar_imagem.lista_de_imagens_publicadas.ultima_pagina);
        const lista_de_paginas = new Array(0);
        
        if(inicio_da_sequencia != 1){
          lista_de_paginas.push("...");
        }
        
        for(var pagina=inicio_da_sequencia; pagina <= final_da_sequencia; pagina++){
          lista_de_paginas.push(pagina);
        }
        
        if(final_da_sequencia != this.vue_publicar_imagem.lista_de_imagens_publicadas.ultima_pagina){
          lista_de_paginas.push("...");
        }
        
        this.lista_de_paginas = lista_de_paginas;
      },
      calcula_quantidade_de_imagens_e_de_espacos_vazios(){
        const quantidade_maxima = this.vue_publicar_imagem.lista_de_imagens_publicadas.quantidade_por_pagina;
        this.quantidade_de_imagens = this.vue_publicar_imagem.lista_de_imagens_publicadas.imagens.length;
        if(this.quantidade_de_imagens === 0){
          this.quantidade_de_espacos_vazios = 0;
        }else{
          this.quantidade_de_espacos_vazios = Math.max(quantidade_maxima - this.quantidade_de_imagens, 0);
        }
      },
      mostrar_nome_do_arquivo_escolhido(evento){
        let nome_do_arquivo = evento.currentTarget.value;
        
        let posicao_apos_ultima_barra = nome_do_arquivo.lastIndexOf("/") + 1;
        nome_do_arquivo = nome_do_arquivo.substring(posicao_apos_ultima_barra);
        
        let posicao_apos_ultima_barra_invertida = nome_do_arquivo.lastIndexOf("\\") + 1;
        nome_do_arquivo = nome_do_arquivo.substring(posicao_apos_ultima_barra_invertida);
        
        this.nome_do_arquivo_que_sera_enviado = nome_do_arquivo;
        
        let caminho_do_arquivo = URL.createObjectURL(evento.currentTarget.files[0]);
        this.caminho_do_arquivo_que_sera_enviado = caminho_do_arquivo;
      },
      remover_foco_do_botao(evento){
        evento.currentTarget.blur();
      },
      mudar_pagina(evento){
        evento.preventDefault();
        
        const href = evento.currentTarget.getAttribute("href");
        const pagina = href.replace("/publicar_imagem?pagina=", "");
        
        this.contador_ajax++;
        this.status_da_lista = "Mudando de página...";
        if(pagina == this.vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual){
          this.status_da_lista = "Atualizando a lista...";
        }
        
        const numero_desta_acao_ajax = this.contador_ajax;
        
        let posicao_vertical_desta_tag = evento.currentTarget.getBoundingClientRect().top + window.scrollY;
        const div_imagens_publicadas = document.getElementById("div_imagens_publicadas");
        let posicao_vertical_da_div_imagens_publicadas = div_imagens_publicadas.getBoundingClientRect().top + window.scrollY;
        if(posicao_vertical_desta_tag > posicao_vertical_da_div_imagens_publicadas){
          const div_local_das_imagens_publicadas = document.getElementById("div_local_das_imagens_publicadas");
          let posicao_vertical_da_div_local_das_imagens_publicadas = div_local_das_imagens_publicadas.getBoundingClientRect().top + window.scrollY;
          window.scrollTo(0, posicao_vertical_da_div_local_das_imagens_publicadas - 4);
        }
        
        /* Requisição ajax */
        let conexao_ajax = null;
        if(window.XMLHttpRequest){
          conexao_ajax = new XMLHttpRequest();
        }else if(window.ActiveXObject){
          conexao_ajax = new ActiveXObject("Microsoft.XMLHTTP");
        }
        const tipo = "GET";
        let url_mais = "";
        url_mais += "?pagina=" + pagina;
        let url = "/publicar_imagem/mostrar_imagens_publicadas_ajax" + url_mais;
        let dados_post = null;
        let resposta = null;
        conexao_ajax.onreadystatechange = function(){
          if(conexao_ajax.readyState == 4){
            if(conexao_ajax.status == 200){
              if(numero_desta_acao_ajax >= this.contador_ajax){
                resposta = JSON.parse(conexao_ajax.responseText);
                this.vue_publicar_imagem.lista_de_imagens_publicadas = resposta.lista_de_imagens_publicadas;
                this.status_da_lista = "";
                this.criar_lista_de_paginas();
                this.calcula_quantidade_de_imagens_e_de_espacos_vazios();
              }
            }
          }
        }.bind(this);
        conexao_ajax.open(tipo, url, true);
        conexao_ajax.setRequestHeader("Content-Type", "application/json");
        conexao_ajax.setRequestHeader("X-CSRF-TOKEN", this.vue_template_layout.chave_anti_csrf);
        conexao_ajax.send(JSON.stringify(dados_post));
      }
    }
  }
</script>

<template>
  <Head>
    <title>Publicar Imagem</title>
  </Head>
  <TemplateLayout :template_layout="vue_template_layout">
    <template #conteudo>
      <div id="div_pagina_do_sistema">
        <h2 id="h2_titulo_da_pagina">
          <span>Publicar Imagem</span>
        </h2>
        <div id="div_mensagem" v-if="vue_publicar_imagem.mensagem">
          <span id="span_mensagem">{{vue_publicar_imagem.mensagem}}</span>
        </div>
        <form id="form_enviar_imagem" method="post" action="/publicar_imagem/publicar_imagem" 
              enctype="multipart/form-data">
          <div id="div_do_campo_enviar_imagem">
            <label id="label_enviar_imagem" for="campo_enviar_imagem">
              <span>Selecione uma imagem</span>
            </label>
            <label id="label_campo_enviar_imagem" for="campo_enviar_imagem">
              <span id="span_instrucao_de_envio_da_imagem">
                Clique para selecionar ou arraste a imagem para esta área
              </span>
              <br/>
              <br/>
              <br/>
              <img id="imagem_arquivo_escolhido_para_envio_de_imagem" :alt="nome_do_arquivo_que_sera_enviado" 
                   :src="caminho_do_arquivo_que_sera_enviado"/>
            </label>
            <input type="file" id="campo_enviar_imagem" name="imagem" required="required" 
                   @change="mostrar_nome_do_arquivo_escolhido"/>
          </div>
          <div id="div_do_botao_enviar_imagem">
            <input type="hidden" name="_token" :value="vue_template_layout.chave_anti_csrf"/>
            <input type="submit" id="botao_enviar_imagem" @mouseleave="remover_foco_do_botao" 
                   @click="remover_foco_do_botao" value="Publicar"/>
          </div>
        </form>
        <div id="div_local_das_imagens_publicadas">
          <h3 id="h3_titulo_das_imagens_publicadas">
            <span id="span_titulo_das_imagens_publicadas">Imagens Publicadas</span>
            <span id="span_status_da_lista">{{status_da_lista}}</span>
          </h3>
          <div v-if="vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual" 
               id="div_paginacao_de_cima_das_imagens_publicadas" class="paginacao_das_imagens_publicadas">
            <a class="primeira_pagina" href="/publicar_imagem?pagina=1" @click="mudar_pagina">Primeira</a>
            <span>&nbsp;</span>
            <a v-if="vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual > 1" 
               :href="'/publicar_imagem?pagina=' + (vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual - 1)" 
               class="pagina_anterior" @click="mudar_pagina">Anterior</a>
            <a v-else class="pagina_anterior" href="/publicar_imagem?pagina=1" @click="mudar_pagina">Anterior</a>
            <span>&nbsp;</span>
            <template v-for="pagina in lista_de_paginas">
              <span v-if="pagina === '...'">...</span>
              <a v-else-if="pagina == vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual" 
                 class="pagina_selecionada" :href="'/publicar_imagem?pagina=' + pagina" 
                 @click="mudar_pagina">{{pagina}}</a>
              <a v-else class="pagina" :href="'/publicar_imagem?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <span>&nbsp;</span>
            </template>
            <a v-if="vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual < vue_publicar_imagem.lista_de_imagens_publicadas.ultima_pagina" 
               :href="'/publicar_imagem?pagina=' + (vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual + 1)" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <a v-else :href="'/publicar_imagem?pagina=' + vue_publicar_imagem.lista_de_imagens_publicadas.ultima_pagina" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <span>&nbsp;</span>
            <a :href="'/publicar_imagem?pagina=' + vue_publicar_imagem.lista_de_imagens_publicadas.ultima_pagina" 
               class="ultima_pagina" @click="mudar_pagina">Última</a>
          </div>
          <div id="div_imagens_publicadas">
            <template v-for="(imagem, chave) in vue_publicar_imagem.lista_de_imagens_publicadas.imagens">
              <div :class="['local_da_imagem', 
                            chave < 3 ? 'um_dos_tres_primeiros' : '', 
                            (chave + 1) % 2 !== 0 ? 'local_impar' : 'local_par', 
                            (chave + 3) % 3 === 0 ? 'primeiro_de_uma_trinca' : '', 
                            (chave + 2) % 3 === 0 ? 'segundo_de_uma_trinca' : '', 
                            (chave + 1) % 3 === 0 ? 'terceiro_de_uma_trinca' : '', 
                            Math.floor(chave / 3) % 2 === 0 ? 'na_trinca_impar' : 'na_trinca_par']">
                <a class="link_da_imagem_publicada" :title="imagem.nome" :href="imagem.endereco">
                  <img class="imagem_publicada" :alt="imagem.endereco" :src="imagem.endereco"/>
                </a>
              </div>
            </template>
            <template v-for="(espaco_vazio, chave) in (new Array(quantidade_de_espacos_vazios))">
              <div :class="['local_do_espaco_vazio', 
                            (chave + quantidade_de_imagens) < 3 ? 'um_dos_tres_primeiros' : '', 
                            (chave + quantidade_de_imagens + 1) % 2 !== 0 ? 'local_impar' : 'local_par', 
                            (chave + quantidade_de_imagens + 3) % 3 === 0 ? 'primeiro_de_uma_trinca' : '', 
                            (chave + quantidade_de_imagens + 2) % 3 === 0 ? 'segundo_de_uma_trinca' : '', 
                            (chave + quantidade_de_imagens + 1) % 3 === 0 ? 'terceiro_de_uma_trinca' : '', 
                            Math.floor((chave + quantidade_de_imagens) / 3) % 2 === 0 ? 'na_trinca_impar' : 'na_trinca_par']">
                <div class="espaco_vazio"></div>
              </div>
            </template>
            <div v-if="vue_publicar_imagem.lista_de_imagens_publicadas.imagens.length === 0" 
                 id="div_mensagem_quando_nao_ha_imagens_publicadas">
              <span id="span_mensagem_quando_nao_ha_imagens_publicadas">Nenhuma imagem foi publicada ainda.</span>
            </div>
          </div>
          <div v-if="vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual"
               id="div_paginacao_de_baixo_das_imagens_publicadas" class="paginacao_das_imagens_publicadas">
            <a class="primeira_pagina" href="/publicar_imagem?pagina=1" @click="mudar_pagina">Primeira</a>
            <span>&nbsp;</span>
            <a v-if="vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual > 1" 
               :href="'/publicar_imagem?pagina=' + (vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual - 1)" 
               class="pagina_anterior" @click="mudar_pagina">Anterior</a>
            <a v-else class="pagina_anterior" href="/publicar_imagem?pagina=1" @click="mudar_pagina">Anterior</a>
            <span>&nbsp;</span>
            <template v-for="pagina in lista_de_paginas">
              <span v-if="pagina === '...'">...</span>
              <a v-else-if="pagina == vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual" 
                 class="pagina_selecionada" :href="'/publicar_imagem?pagina=' + pagina" 
                 @click="mudar_pagina">{{pagina}}</a>
              <a v-else class="pagina" :href="'/publicar_imagem?pagina=' + pagina" @click="mudar_pagina">{{pagina}}</a>
              <span>&nbsp;</span>
            </template>
            <a v-if="vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual < vue_publicar_imagem.lista_de_imagens_publicadas.ultima_pagina" 
               :href="'/publicar_imagem?pagina=' + (vue_publicar_imagem.lista_de_imagens_publicadas.pagina_atual + 1)" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <a v-else :href="'/publicar_imagem?pagina=' + vue_publicar_imagem.lista_de_imagens_publicadas.ultima_pagina" 
               class="pagina_seguinte" @click="mudar_pagina">Seguinte</a>
            <span>&nbsp;</span>
            <a :href="'/publicar_imagem?pagina=' + vue_publicar_imagem.lista_de_imagens_publicadas.ultima_pagina" 
               class="ultima_pagina" @click="mudar_pagina">Última</a>
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
  @media(max-width: 640px){
    #div_mensagem{
      margin-top: 15px;
      font-size: 11pt;
    }
  }
  #form_enviar_imagem{
    margin-top: 30px;
    font-size: 14pt;
  }
  @media(max-width: 880px){
    #form_enviar_imagem{
      margin-top: 20px;
    }
  }
  @media(max-width: 640px){
    #form_enviar_imagem{
      margin-top: 15px;
      font-size: 12pt;
    }
  }
  #div_do_campo_enviar_imagem{
    position: relative;
    margin-right: auto;
    margin-left: auto;
    width: 500px;
    max-width: 100%;
    overflow: hidden;
  }
  #label_enviar_imagem{
    display: block;
    color: #424242;
    cursor: pointer;
  }
  #label_campo_enviar_imagem{
    display: block;
    position: relative;
    float: left;
    margin-top: 3px;
    border: 1px solid #ABABAB;
    border-radius: 14px;
    padding: 8px 10px;
    width: calc(500px - 2px - 20px);
    max-width: calc(100% - 2px - 20px);
    min-height: calc(250px - 2px - 16px);
    max-height: calc(250px - 2px - 16px);
    background-color: #FFF;
    font-size: 11pt;
    text-align: center;
    text-overflow: ellipsis;
    overflow: hidden;
    word-wrap: break-word;
    cursor: pointer;
  }
  #imagem_arquivo_escolhido_para_envio_de_imagem{
    max-width: 300px;
    max-height: 150px;
    font-size: 11pt;
  }
  #campo_enviar_imagem{
    display: block;
    position: absolute;
    float: left;
    margin-top: 3px;
    border: none;
    padding: 0px;
    width: 500px;
    max-width: 100%;
    min-height: 250px;
    max-height: 250px;
    opacity: 0;
    z-index: 10;
    cursor: pointer;
  }
  #div_do_botao_enviar_imagem{
    margin-top: 12px;
    margin-right: auto;
    margin-left: auto;
    width: 500px;
    max-width: 100%;
    text-align: right;
  }
  #div_local_das_imagens_publicadas{
    margin-top: 40px;
    margin-right: auto;
    margin-left: auto;
    max-width: 100%;
  }
  #h3_titulo_das_imagens_publicadas{
    margin-right: auto;
    margin-left: auto;
    max-width: 100%;
    font-size: 18pt;
  }
  #span_titulo_das_imagens_publicadas{
    color: #6A6A6A;
  }
  #span_status_da_lista{
    position: relative;
    top: -1px;
    margin-left: 20px;
    color: #101010;
    font-size: 12pt;
  }
  @media(max-width: 500px){
    #span_status_da_lista{
      display: block;
      top: 4px;
      margin-left: 0px;
      min-height: 20px;
    }
  }
  #div_paginacao_de_cima_das_imagens_publicadas{
    margin-top: 4px;
    text-align: center;
    white-space: nowrap;
    overflow: auto;
  }
  @media(max-width: 640px){
    #div_paginacao_de_cima_das_imagens_publicadas{
      position: relative;
      padding-bottom: 10px;
      font-size: 10.5pt;
      z-index: 1;
    }
  }
  .paginacao_das_imagens_publicadas>a,
  .paginacao_das_imagens_publicadas>a:visited{
    display: inline-block;
    border-radius: 8px;
    padding: 4px 7px;
    vertical-align: top;
    color: #484880;
  }
  .paginacao_das_imagens_publicadas>a:hover{
    background-color: #343458;
    color: #FFF;
    text-decoration: none;
  }
  .pagina_selecionada,
  .pagina_selecionada:visited{
    text-shadow: 1px 0px 0px #343458;
  }
  #div_imagens_publicadas{
    margin-right: auto;
    margin-left: auto;
    border: 8px solid #A0A0F0;
    min-width: calc(100% - 16px);
    max-width: calc(100% - 16px);
    background-color: #A0A0F0;
    overflow: hidden;
  }
  #div_imagens_publicadas>.local_da_imagem,
  #div_imagens_publicadas>.local_do_espaco_vazio{
    position: relative;
    float: left;
    border-top: 8px solid #A0A0F0;
    border-left: 8px solid #A0A0F0;
    background-color: #FFF;
  }
  @media(min-width: 1711px){
    #div_imagens_publicadas>.local_da_imagem,
    #div_imagens_publicadas>.local_do_espaco_vazio{
      padding-top: calc(100% / 6 - 16px / 6);
      min-width: calc(100% / 3 - 16px / 3);
      max-width: calc(100% / 3 - 16px / 3);
    }
    #div_imagens_publicadas>.um_dos_tres_primeiros{
      border-top: none;
    }
    #div_imagens_publicadas>.primeiro_de_uma_trinca{
      border-left: none;
    }
  }
  @media(min-width: 1521px) and (max-width: 1710px){
    #div_imagens_publicadas>.local_da_imagem,
    #div_imagens_publicadas>.local_do_espaco_vazio{
      padding-top: calc(100% / 4 - 8px / 4);
      min-width: calc(100% / 2 - 8px / 2);
      max-width: calc(100% / 2 - 8px / 2);
    }
    #div_imagens_publicadas>.um_dos_tres_primeiros.primeiro_de_uma_trinca,
    #div_imagens_publicadas>.um_dos_tres_primeiros.segundo_de_uma_trinca{
      border-top: none;
    }
    #div_imagens_publicadas>.local_impar{
      border-left: none;
    }
  }
  @media(min-width: 1321px) and (max-width: 1520px){
    #div_imagens_publicadas>.local_da_imagem,
    #div_imagens_publicadas>.local_do_espaco_vazio{
      padding-top: calc(100% / 6 - 16px / 6);
      min-width: calc(100% / 3 - 16px / 3);
      max-width: calc(100% / 3 - 16px / 3);
    }
    #div_imagens_publicadas>.um_dos_tres_primeiros{
      border-top: none;
    }
    #div_imagens_publicadas>.primeiro_de_uma_trinca{
      border-left: none;
    }
  }
  @media(min-width: 961px) and (max-width: 1320px){
    #div_imagens_publicadas>.local_da_imagem,
    #div_imagens_publicadas>.local_do_espaco_vazio{
      padding-top: calc(100% / 4 - 8px / 4);
      min-width: calc(100% / 2 - 8px / 2);
      max-width: calc(100% / 2 - 8px / 2);
    }
    #div_imagens_publicadas>.um_dos_tres_primeiros.primeiro_de_uma_trinca,
    #div_imagens_publicadas>.um_dos_tres_primeiros.segundo_de_uma_trinca{
      border-top: none;
    }
    #div_imagens_publicadas>.local_impar{
      border-left: none;
    }
  }
  @media(min-width: 881px) and (max-width: 960px){
    #div_imagens_publicadas>.local_da_imagem,
    #div_imagens_publicadas>.local_do_espaco_vazio{
      border-left: none;
      padding-top: 50%;
      min-width: 100%;
      max-width: 100%;
    }
    #div_imagens_publicadas>.um_dos_tres_primeiros.primeiro_de_uma_trinca{
      border-top: none;
    }
  }
  @media(min-width: 641px) and (max-width: 880px){
    #div_imagens_publicadas>.local_da_imagem,
    #div_imagens_publicadas>.local_do_espaco_vazio{
      padding-top: calc(100% / 4 - 8px / 4);
      min-width: calc(100% / 2 - 8px / 2);
      max-width: calc(100% / 2 - 8px / 2);
    }
    #div_imagens_publicadas>.um_dos_tres_primeiros.primeiro_de_uma_trinca,
    #div_imagens_publicadas>.um_dos_tres_primeiros.segundo_de_uma_trinca{
      border-top: none;
    }
    #div_imagens_publicadas>.local_impar{
      border-left: none;
    }
  }
  @media(max-width: 640px){
    #div_imagens_publicadas>.local_da_imagem,
    #div_imagens_publicadas>.local_do_espaco_vazio{
      border-left: none;
      padding-top: 50%;
      min-width: 100%;
      max-width: 100%;
    }
    #div_imagens_publicadas>.um_dos_tres_primeiros.primeiro_de_uma_trinca{
      border-top: none;
    }
  }
  .link_da_imagem_publicada{
    display: flex;
    position: absolute;
    top: 0;
    left: 0;
    min-width: 100%;
    max-width: 100%;
    min-height: 100%;
    max-height: 100%;
    overflow: hidden;
  }
  .imagem_publicada{
    margin: auto;
    max-width: 100%;
    max-height: 300px;
  }
  .espaco_vazio{
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    min-width: 100%;
    max-width: 100%;
    min-height: 100%;
    max-height: 100%;
  }
  #div_mensagem_quando_nao_ha_imagens_publicadas{
    padding: 10px 8px;
    background-color: #FFF;
    text-align: center;
  }
  #div_paginacao_de_baixo_das_imagens_publicadas{
    text-align: center;
    white-space: nowrap;
    overflow: auto;
  }
  @media(max-width: 640px){
    #div_paginacao_de_baixo_das_imagens_publicadas{
      position: relative;
      padding-bottom: 10px;
      font-size: 10.5pt;
      z-index: 1;
    }
  }
</style>

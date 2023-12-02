<script>
  import {Head} from "@inertiajs/vue3";
  import TemplateLayout from "../template_layout.vue";
  
  export default{
    props: [
      "template_layout"
    ],
    components: {
      Head,
      TemplateLayout
    },
    data(){
      return{
        /* Propriedades obtidas dos controllers precisam ser recolocadas para prevenir o cache do inertia */
        vue_template_layout: this.template_layout
      }
    },
    methods: {
      desfaz_selecao_de_texto(evento){
        evento.preventDefault();
      },
      expandir_ou_encolher_instrucoes(evento){
        const tag_que_disparou_o_evento = evento.currentTarget;
        const tag_alvo = tag_que_disparou_o_evento.parentNode.getElementsByClassName("instrucoes")[0];
        if(tag_alvo.classList.contains("instrucoes_encolhidas")){
          tag_alvo.classList.remove("instrucoes_encolhidas");
          tag_alvo.classList.add("instrucoes_expandidas");
        }else{
          tag_alvo.classList.remove("instrucoes_expandidas");
          tag_alvo.classList.add("instrucoes_encolhidas");
        }
      }
    }
  }
</script>

<template>
  <Head>
    <title>Página Inicial</title>
  </Head>
  <TemplateLayout :template_layout="vue_template_layout">
    <template #conteudo>
      <div id="div_pagina_do_sistema">
        <h2 id="h2_titulo_da_pagina">
          <span>Página Inicial</span>
        </h2>
        <div id="div_sobre_este_sistema">
          <p>
            Este sistema tem por finalidade mostrar alguns serviços de outros sistemas. Para que os serviços externos 
            funcionem, é necessário configurar algumas informações no arquivo .env deste sistema. Siga as instruções 
            abaixo para configurar o arquivo .env corretamente, expanda e leia a instrução do serviço correspondente. 
            Após cada configuração feita no arquivo .env, selecione o serviço relacionado pelo menu para ver o resultado.
          </p>
        </div>
        <div id="div_instrucoes_de_configuracao">
          <h2 id="h3_titulo_instrucoes_de_configuracao">
            <span id="span_titulo_instrucoes_de_configuracao">Instruções</span>
            <br/>
            <span id="span_subtitulo_instrucoes_de_configuracao">Clique ou toque para expandir</span>
          </h2>
          <div id="div_local_das_instrucoes_do_servico_publicar_imagem" class="local_das_instrucoes">
            <h4 id="h4_parte_de_cima_do_titulo_das_instrucoes_do_servico_publicar_imagem" 
                class="parte_de_cima_do_titulo_das_instrucoes" @click="expandir_ou_encolher_instrucoes" 
                @mousedown="desfaz_selecao_de_texto">
              <span>Publicar Imagem</span>
            </h4>
            <div id="div_instrucoes_do_servico_publicar_imagem" class="instrucoes instrucoes_encolhidas">
              <p>
                Publicar Imagem (Serviço do Imgur) (23/10/2023)
              </p>
              <p>
                A funcionalidade Publicar Imagem deste sistema é possível graças ao serviço do Imgur 
                <a href="https://imgur.com">https://imgur.com</a>
                <br/>
                Primeiro de tudo é necessário se registrar no Imgur normalmente pela página:
                <a href="https://imgur.com/register">https://imgur.com/register</a>
                <br/>
                Após se registrar, faça login pela página de login:
                <a href="https://imgur.com/signin">https://imgur.com/signin</a>
              </p>
              <p>
                O primeiro passo para que este sistema possa utilizar o serviço do Imgur é a etapa de registrar 
                o aplicativo (registrar este sistema no Imgur). 
              </p>
              <p>
                Acesse <a href="https://api.imgur.com/oauth2/addclient">https://api.imgur.com/oauth2/addclient</a> e 
                preencha o formulário para registrar este sistema.
              </p>
              <p>
                No campo Application name, preencha com o nome que você usa para identificar este sistema.
                <br/>
                Exemplo: "Sistema Diversos Serviços Web".
                <br/>
                <br/>
                Em Authorization type, qualquer uma das três opções serve, mas para este sistema "Anonymous usage 
                without user authorization" é o suficiente, abaixo apresento uma breve descrição sobre cada opção:
                <br/>
                <br/>
                OAuth 2 authorization with a callback URL
                <br/>
                Se este sistema tivesse autenticação do Imgur juntamente com o serviço, essa seria a opção indicada. 
                Caso implemente isso e queira escolher essa opção, será necessário colocar no formulário a URL deste
                sistema responsável por tratar a autenticação no campo callback URL.
                <br/>
                <br/>
                OAuth 2 authorization without a callback URL
                <br/>
                Autenticação sem especificar um callback URL fará com que redirecione para a página inicial do próprio 
                Imgur, onde os valores access_token, expires_in, token_type, refresh_token, account_username e 
                account_id irão aparecer na própria URL da página inicial do Imgur como parâmetros.
                <br/>
                <br/>
                Anonymous usage without user authorization
                <br/>
                Escolha esta opção, esta opção é o suficiente para este sistema e indica que a intenção é somente 
                publicar imagens. Um envio de imagem sem ter um usuário autenticado pelo Imgur é considerado pelo Imgur 
                como um envio anônimo e também é possível nas outras duas opções.
              </p>
              <p>
                O campo Authorization callback URL só precisa ser preenchido caso tenha escolhido e implementado 
                "OAuth 2 authorization with a callback URL". Se esse for o caso, preencha com a URL deste sistema 
                responsável por tratar a autenticação.
                <br/>
                No nosso caso, "Anonymous usage without user authorization", deixe o campo Authorization callback URL 
                sem preencher. Se mesmo assim, o Imgur indicar que este campo é de preenchimento obrigatório, preencha 
                com algum valor permitido, você poderá mudar o valor depois. Exemplo: preencha com https://imgur.com
              </p>
              <p>
                O campo Application website é opcional e é o endereço deste sistema ou o site da sua empresa (tanto faz).
              </p>
              <p>
                No campo Email, preencha com o seu e-mail ou com o e-mail da sua empresa (tanto faz).
              </p>
              <p>
                No campo Description, preencha descrevendo de forma breve o que este sistema faz.
                <br/>
                Exemplo: "Este sistema publica imagens no Imgur".
              </p>
              <p>
                Após o envio do formulário, irá aparecer uma página com os valores:
                <br/>
                client_id
                <br/>
                client_secret
                <br/>
                <br/>
                Anote os valores.
              </p>
              <p>
                No arquivo .env deste sistema existem as seguintes configurações relacionadas ao uso do serviço do Imgur:
                <br/>
                IMGUR_CLIENT_ID
                <br/>
                IMGUR_CLIENT_SECRET
                <br/>
                IMGUR_ACCESS_TOKEN
                <br/>
                IMGUR_TOKEN_TYPE
                <br/>
                IMGUR_REFRESH_TOKEN
                <br/>
                <br/>
                A única configuração que precisa ter valor para ser possível publicar imagem no Imgur é a configuração: 
                IMGUR_CLIENT_ID. Não precisa colocar valor nas outras configurações, pois só estão especificadas para 
                possíveis futuras implementações neste sistema.
              </p>
              <p>
                Para consultar os aplicativos (sistemas) que você registrou, acesse:
                <br/>
                <a href="https://imgur.com/account/settings/apps">https://imgur.com/account/settings/apps</a>
              </p>
              <p>
                Você pode registrar mais aplicativos (sistemas) pelo endereço:
                <br/>
                <a href="https://api.imgur.com/oauth2/addclient">https://api.imgur.com/oauth2/addclient</a>
              </p>
            </div>
            <h4 id="h4_parte_de_baixo_do_titulo_das_instrucoes_do_servico_publicar_imagem" 
                class="parte_de_baixo_do_titulo_das_instrucoes" @click="expandir_ou_encolher_instrucoes" 
                @mousedown="desfaz_selecao_de_texto">
              <span>Publicar Imagem</span>
            </h4>
          </div>
          <div id="div_local_das_instrucoes_do_servico_login_com_facebook" class="local_das_instrucoes">
            <h4 id="h4_parte_de_cima_do_titulo_das_instrucoes_do_servico_login_com_facebook" 
                class="parte_de_cima_do_titulo_das_instrucoes" @click="expandir_ou_encolher_instrucoes" 
                @mousedown="desfaz_selecao_de_texto">
              <span>Login com Facebook</span>
            </h4>
            <div id="div_instrucoes_do_servico_login_com_facebook" class="instrucoes instrucoes_encolhidas">
              <p>
                Login (Serviço do Facebook) (25/11/2023)
              </p>
              <p>
                Primeiro de tudo: Configure SSL (Secure Sockets Layer) ou TLS (Transport Layer Security) no seu 
                ambiente de desenvolvimento, pois para utilizar o serviço de login do Facebook é necessário HTTPS.
              </p>
              <p>
                Faça login no Facebook.
              </p>
              <p>
                Acesse <a href="https://developers.facebook.com/apps">https://developers.facebook.com/apps</a>.
              </p>
              <p>
                Se registre como desenvolvedor.
              </p>
              <p>
                Clique em "Criar aplicativo".
              </p>
              <p>
                Selecione:
                <br/>
                "Permitir que as pessoas entrem com a própria conta 
                do Facebook Nosso caso de uso mais comum. Uma maneira 
                segura, rápida e fácil para os usuários entrarem no app 
                e para o app solicitar ao usuário permissões de acesso 
                aos dados."
              </p>
              <p>
                Clique em "avançar".
              </p>
              <p>
                Preencha o formulário: nome do app e e-mail para contato.
              </p>
              <p>
                Clique em "Criar aplicativo" (para você poder registrar seu aplicativo/sistema) no Facebook.
              </p>
              <p>
                Você precisará confirmar inserindo sua senha do Facebook.
              </p>
              <p>
                Após inserir, clique em "Enviar".
              </p>
              <p>
                Você será redirecionado para uma página de painel do seu aplicativo:
                <br/>
                https://developers.facebook.com/apps/ID_DO_SEU_APLICATIVO/dashboard/
              </p>
              <p>
                Copie o ID_DO_SEU_APLICATIVO e coloque como valor da chave FACEBOOK_ID_DO_APP no arquivo .env deste 
                sistema.
              </p>
              <p>
                Clique em "Casos de uso".
              </p>
              <p>
                Você será redirecionado para a página:
                <br/>
                https://developers.facebook.com/apps/ID_DO_SEU_APLICATIVO/use_cases/
              </p>
              <p>
                Em "Autenticação e criação de conta", clique em "Personalizar".
              </p>
              <p>
                Você será redirecionado para a página:
                <br/>
                https://developers.facebook.com/apps/ID_DO_SEU_APLICATIVO/permissions?use_case_enum=FB_LOGIN
              </p>
              <p>
                Em "Configurações", clique em "Acessar configurações".
              </p>
              <p>
                Em "URIs de redirecionamento do OAuth válidos", adicione o endereço do seu sistema, exemplo:
                <br/>
                https://servicos-laravel-vue-inertia.local/login_com_facebook
              </p>
              <p>
                Marque "Sim" para "Entrar com o SDK do JavaScript".
              </p>
              <p>
                Em "Domínios permitidos para o SDK do JavaScript", adicione o domínio do seu sistema, exemplo:
                <br/>
                https://servicos-laravel-vue-inertia.local
              </p>
              <p>
                Salve as configurações.
              </p>
              <p>
                Estando novamente na página do painel (dashboard), clique em "Configurações do app".
              </p>
              <p>
                Na parte de configurações básicas, em "Domínios do aplicativo", adicione o domínio do seu sistema, 
                exemplo:
                <br/>
                https://servicos-laravel-vue-inertia.local
              </p>
              <p>
                Salve as configurações.
              </p>
              <p>
                Lembre-se que seu ambiente de desenvolvimento precisa estar com SSL ou TLS, pois só irá funcionar se 
                estiver com https.
              </p>
              <p>
                Agora você já é capaz de fazer login no seu sistema com a sua conta do Facebook.
              </p>
              <p>
                Se tudo estiver certo, porém aparecer erro relacionado à CORS no seu navegador, desative plugins 
                (addons de navegador) dos tipos bloqueador de anúncios e antirrastreio, e tente fazer login novamente.
              </p>
              <p>
                Observações:
                <br/>
                No final da página do painel (dashboard), tem a opção para publicar/lançar o aplicativo, 
                você precisará publicar/lançar o aplicativo para que outras pessoas (outras contas de Facebook)
                possam utilizar o serviço de login do Facebook no seu sistema;
                <br/>
                Se você quiser, você pode criar um aplicativo de testes (vertente do aplicativo real) para 
                deixar configurado para ambiente de desenvolvimento enquanto deixa o aplicativo real configurado 
                para ambiente de produção. Para tal, na lista de aplicativos, no aplicativo que você criou, clique 
                em "..." e depois clique em "Criar aplicativo de teste";
                <br/>
                No final da página de configurações avançadas é possível deletar o aplicativo criado.
              </p>
              <p>
                Se tiver dúvida, consulte o tutorial do próprio Facebook:
                <br/>
                <a href="https://developers.facebook.com/docs/facebook-login/web">https://developers.facebook.com/docs/facebook-login/web</a>
              </p>
              <p>
                Página da documentação:
                <br/>
                <a href="https://developers.facebook.com/docs/facebook-login/">https://developers.facebook.com/docs/facebook-login/</a>
              </p>
            </div>
            <h4 id="h4_parte_de_baixo_do_titulo_das_instrucoes_do_servico_login_com_facebook" 
                class="parte_de_baixo_do_titulo_das_instrucoes" @click="expandir_ou_encolher_instrucoes" 
                @mousedown="desfaz_selecao_de_texto">
              <span>Login com Facebook</span>
            </h4>
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
  #div_sobre_este_sistema{
    margin-top: 30px;
    color: #424242;
    font-size: 14pt;
    text-align: justify;
  }
  @media(max-width: 880px){
    #div_sobre_este_sistema{
      margin-top: 20px;
    }
  }
  @media(max-width: 640px){
    #div_sobre_este_sistema{
      margin-top: 15px;
      font-size: 12pt;
    }
  }
  #div_sobre_este_sistema>p{
    margin-top: 26px;
    line-height: 28px;
    text-indent: 70px;
  }
  @media(max-width: 640px){
    #div_sobre_este_sistema>p{
      margin-top: 10px;
      line-height: 22px;
      text-indent: 35px;
    }
  }
  #div_sobre_este_sistema>p:first-child{
    margin-top: 0px;
  }
  #h3_titulo_instrucoes_de_configuracao{
    margin-top: 30px;
    font-size: 18pt;
  }
  @media(max-width: 640px){
    #h3_titulo_instrucoes_de_configuracao{
      margin-top: 20px;
      font-size: 16pt;
    }
  }
  #span_titulo_instrucoes_de_configuracao{
    color: #6A6A6A;
  }
  #span_subtitulo_instrucoes_de_configuracao{
    color: #424242;
    font-size: 12pt;
  }
  .local_das_instrucoes{
    margin-top: 20px;
  }
  .parte_de_cima_do_titulo_das_instrucoes{
    max-height: 32px;
    background-color: #506070;
    color: #FFF;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    box-shadow: inset 0px 41px 28px -40px #343458, inset 0px -41px 28px -40px #6060A0;
    cursor: pointer;
  }
  .parte_de_cima_do_titulo_das_instrucoes>span{
    position: relative;
    top: 14px;
    font-size: 20pt;
    text-align: center;
  }
  .instrucoes{
    border-right: 5px solid #507080;
    border-left: 5px solid #507080;
    overflow: hidden;
  }
  .instrucoes>p{
    margin-top: 26px;
    padding: 0px 8px;
    line-height: 28px;
    font-size: 14pt;
  }
  @media(max-width: 640px){
    .instrucoes>p{
      line-height: 22px;
      font-size: 12pt;
    }
  }
  .instrucoes>p:first-child{
    margin-top: 0px;
  }
  .instrucoes_encolhidas{
    max-height: 0px;
    transition: max-height 0.5s ease-out;
  }
  .instrucoes_expandidas{
    max-height: 2800px;
    transition: max-height 0.5s ease-in;
    overflow: auto;
  }
  .parte_de_baixo_do_titulo_das_instrucoes{
    max-height: 32px;
    background-color: #506070;
    color: #FFF;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    box-shadow: inset 0px 41px 28px -40px #6060A0, inset 0px -41px 28px -40px #343458;
    cursor: pointer;
  }
  .parte_de_baixo_do_titulo_das_instrucoes>span{
    position: relative;
    bottom: 18px;
    font-size: 20pt;
    text-align: center;
  }
</style>

<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">

        <!--Inicio do cartão de pesquisa-->

        <!-- impressão e teste da variável global "teste" através do VUEX -->
        <!-- {{ $store.state.teste}} -->
        <!-- <button @click="$store.state.teste = 'Alterei o valor da variavel teste'">teste</button> -->

        <card-component titulo="Procura de marcas">

          <template v-slot:conteudo>
            <div class="row">
              <div class="col mb-3">
                <input-container-component id="inputId" titulo="ID" help="idHelp"
                  textoAjuda="Opecional. Informe o ID da Marca">
                  <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID"
                    v-model="busca.id">
                </input-container-component>
              </div>
              <div class="col mb-3">
                <input-container-component id="inputNome" titulo="Nome" help="nomeHelp"
                  textoAjuda="Opecional. Informe o nome da Marca">
                  <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp"
                    placeholder="Nome da Marca" v-model="busca.nome">
                </input-container-component>
              </div>
            </div>
          </template>

          <template v-slot:rodape>
            <button type="submit" class="btn btn-primary btn-sm float-end" @click="pesquisar()">Pesquisar</button>
          </template>

        </card-component>
        <!--Fim do cartão de pesquisa-->

        <!--Inicio do cartão de listagem de marcas-->
        <card-component titulo="Relação de marcas">

          <template v-slot:conteudo>
            <table-component :dados=marcas.data :titulos="['ID', 'Nome', 'Imagem', 'Data de criação']"
              :visualizar="{ visivel: true, dataToogle: 'modal', dataTarget: '#modalMarcaVisualizar' }" 
              :atualizar="{ visivel: true, dataToogle: 'modal', dataTarget: '#modalMarcaAtualizar' }" 
              :remover="{ visivel: true, dataToogle: 'modal', dataTarget: '#modalMarcaRemover' }"></table-component>
            <!--fazendo um b-vind com o props titulos o vue entende que o array dentro dos parenises é um array e não uma string-->
          </template>

          <template v-slot:rodape>
            <div class="row">
              <div class="col-10">
                <paginate-component>
                  <li v-for="l, key in marcas.links" :key="key" :class="l.active ? 'page-item active' : 'page-item'"
                    @click="paginacao(l)">
                    <a class="page-link" v-html="l.label"></a>
                    <!--o v-html interpreta o texto enviado e interpreta como sendo conteudo html-->
                  </li>
                </paginate-component>
              </div>
              <div class="col">
                <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal"
                  data-bs-target="#modalMarca">Adicionar</button>
              </div>
            </div>
          </template>

        </card-component>
        <!--Fim do cartão de listagem de marcas-->
        <!--Inicio do modal de registo de marcas-->
        <modal-component id="modalMarca" titulo="Adicionar marca">

          <template v-slot:alertas>
            <alert-component tipo="success" :detalhes=transacaoDetalhes titulo="Registo feito com sucesso"
              v-if="transacaoStatus == 'adicionado'"></alert-component>
            <alert-component tipo="danger" :detalhes=transacaoDetalhes titulo="Tentativa de registo falhada"
              v-if="transacaoStatus == 'erro'"></alert-component>
          </template>

          <template v-slot:conteudo>

            <div class="form-group">
              <input-container-component id="novoNome" titulo="Nome da Marca" help="novoNomeHelp"
                textoAjuda="Informe o Nome da Marca">
                <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp"
                  placeholder="Nome da Marca" v-model="nomeMarca">
              </input-container-component>
            </div>

            <div class="form-group">
              <input-container-component id="novoImagem" titulo="Imagem" help="imagemHelp"
                textoAjuda="Adicione uma imagem em formato PNG">
                <input type="file" class="form-control" id="novoImagem" aria-describedby="imagemHelp"
                  placeholder="Adicione uma imagem" @change="carregarImagem($event)">
              </input-container-component>
            </div>

          </template>

          <template v-slot:rodape>

            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>

          </template>
        </modal-component>
        <!--fim do modal de registo de marcas-->

        <!--Inicio do modal de visualização de marcas-->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar marca">

          <template v-slot:alertas>
          </template>

          <template v-slot:conteudo>
            <!--Impreção da visualização de marcas através do vuex(ver app.js em resources)-->
            <input-container-component titulo="Id da marca">
              <input type="text" class="form-control" :value="$store.state.item.id" disabled>
            </input-container-component>

            <input-container-component titulo="Nome da Marca">
              <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
            </input-container-component>

            <input-container-component titulo="Imagem">
              <div>
                <img :src="'storage/' + $store.state.item.imagem" alt="" v-if="$store.state.item.imagem">
              </div>
            </input-container-component>

            <input-container-component titulo="Data de criação">
              <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
            </input-container-component>
          </template>

          <template v-slot:rodape>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          </template>
        </modal-component>
        <!--Fim do modal de visualização de marcas-->

        <!--Inicio do modal de remoção de marcas-->
        <modal-component id="modalMarcaRemover" titulo="Remover marca">

          <template v-slot:alertas>
            <alert-component tipo="success" titulo="Transação realizada com sucesso" :detalhes="$store.state.transacao"
              v-if="$store.state.transacao.status == 'success'"></alert-component>
            <alert-component tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao"
              v-if="$store.state.transacao.status == 'danger'"></alert-component>
          </template>

          <template v-slot:conteudo v-if="$store.state.transacao.status != 'success'">
            <!--Impreção da visualização de marcas através do vuex(ver app.js em resources)-->
            <input-container-component titulo="Id da marca">
              <input type="text" class="form-control" :value="$store.state.item.id" disabled>
            </input-container-component>

            <input-container-component titulo="Nome da Marca">
              <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
            </input-container-component>
          </template>

          <template v-slot:rodape>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-danger" @click="remover()"
              v-if="$store.state.transacao.status != 'success'">Remover</button>
          </template>
        </modal-component>
        <!--Fim do modal de remoção de marcas-->

        <!--Inicio do modal de atualização de marcas-->
        <modal-component id="modalMarcaAtualizar" titulo="Atualizar marca">

          <template v-slot:alertas>
          </template>

          <template v-slot:conteudo>
            <div class="form-group">
              <input-container-component id="atualizarNome" titulo="Nome da Marca" help="atualizarNomeHelp"
                textoAjuda="Informe o Nome da Marca">
                <input type="text" class="form-control" id="atualizarNome" aria-describedby="atualizarNomeHelp"
                  placeholder="Nome da Marca" v-model="$store.state.item.nome">
              </input-container-component>
            </div>

            <div class="form-group">
              <input-container-component id="atualizarImagem" titulo="Imagem" help="atualizarimagemHelp"
                textoAjuda="Adicione uma imagem em formato PNG">
                <input type="file" class="form-control" id="atualizarImagem" aria-describedby="atualizarimagemHelp"
                  placeholder="Adicione uma imagem" @change="carregarImagem($event)">
              </input-container-component>
            </div>
            {{$store.state.item}}
          </template>

          <template v-slot:rodape>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-primary" @click="atualizar()">Atualizar</button>

          </template>
        </modal-component>
        <!--fim do modal de atualização de marcas-->

      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      urlBase: 'http://127.0.0.1:8000/api/v1/marca',
      urlPaginacao: '',
      urlFiltro: '',
      nomeMarca: "",
      arquivoImagem: [],//os inputs de tipo file recebem como parametro um array
      transacaoStatus: '',
      transacaoDetalhes: {},
      marcas: { data: [] },
      busca: { id: '', nome: '' }
    }
  },
  methods: {
    atualizar(){
      let formData = new FormData
      formData.append('_method', 'patch')
      formData.append('nome', this.$store.state.item.nome)

      if(this.arquivoImagem[0]){
        formData.append('imagem', this.arquivoImagem[0])
      }
      let url = this.urlBase + '/' + this.$store.state.item.id

      let config = {
        headers:{
          'Content-Type' : 'multipart/form-data',
        }
      }

      axios.post(url, formData, config)
        .then(response =>{
          console.log('atualizado', response)
          atualizarImagem.value = ""
          this.carregarLista()
        })
        .catch(errors =>{
          console.log('erro de atualização', errors.response)
        })
    },
    remover() {
      let confirmacao = confirm('Tem a certeza que deseja remover este registo?')
      if (!confirmacao) {
        return false
      }
      let url = this.urlBase + '/' + this.$store.state.item.id
      console.log(url)

      let formData = new FormData();
      formData.append('_method', 'delete')



      axios.post(url, formData)
        .then(response => {
          console.log('Registo removido com sucesso', response)
          this.$store.state.transacao.status = 'success'
          this.$store.state.transacao.menssagem = response.data.msg
          this.carregarLista()
        })
        .catch(errors => {
          console.log('Houve um erro na tentativa de remoção do registo', errors.response)
          this.$store.state.transacao.status = 'erro'
          this.$store.state.transacao.menssagem = 'Erro ao tentar remover este registo'
        })

    },
    pesquisar() {
      let filtro = ''

      for (let chave in this.busca) { // percorre todos os parametro enviado na busca

        if (this.busca[chave]) { // dá a garantia de que existe um parametro enviado para montar um parametro de busca válido

          if (filtro != '') { // caso exista um filtro de busca, adiciona um ';' no final
            filtro += ';'
          }

          filtro += chave + ':like:' + this.busca[chave] //incrementa sempre que haja um filtro de busca á variável filtro
        }
      }
      if (filtro != '') {
        this.urlPaginacao = 'page=1'
        this.urlFiltro = '&filtro=' + filtro
      } else {
        this.urlFiltro = ''
      }
      this.carregarLista()
    },
    paginacao(l) {
      if (l.url) {
        this.urlPaginacao = l.url.split('?')[1] // anteriormente era recebido todo o url completo, agora, através do metodo split, é separado e capturado apenas a paginação
        this.carregarLista()
      }
    },
    carregarLista() {
      //carregamento da página acessada dividindo os parametros de pesquisa e paginação. para que a url principal seja sempre estática e fácil de trabalhar
      let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro

      console.log(url)

      axios.get(url)
        .then(response => {
          this.marcas = response.data
          // console.log(this.marcas)
        })
        .catch(errors => {
          console.log(errors)
        })
    },

    carregarImagem(e) {
      this.arquivoImagem = e.target.files
    },
    salvar() {
      console.log(this.nomeMarca, this.arquivoImagem)

      let formData = new FormData(); //pesquisar FormData() indentico ao form do postman
      formData.append('nome', this.nomeMarca);
      formData.append('imagem', this.arquivoImagem[0]);

      let config = { //identico aos headers do postman
        headers: {
          'Content-Type': 'multipart/form-data',//permite a receção de imagens
          // 'Authorization': this.token //caso o token não esteja a ser encaminhado na requesição, forçar o token na autorization
        }
      }

      axios.post(this.urlBase, formData, config)//(url, conteudo, configurações)ver decomentação sobre o axios (este é carregado junto com a framework js) envia a requesição para o backend
        .then(response => {
          this.transacaoStatus = 'adicionado'
          this.transacaoDetalhes = {
            mensagem: "ID nº" + response.data.id + " registado com sucesso"
          }
          console.log(response)
          this.carregarLista()
        })
        .catch(errors => { //apanha os erros espessificos desta requesição 
          this.transacaoStatus = 'erro'
          this.transacaoDetalhes = {
            mensagem: errors.response.data.message // evitar ao maximo a dependencia do vue das respostas dadas, 
          }
          console.log(errors.response)
        })

    }
  },
  mounted() { //após estar montada toda a página o vue vai carregar o metodo
    this.carregarLista()
  },
  computed: {
    //   token(){ //caso o token não esteja a ser encaminhado na requesição, forçar o token na autorization
    //     let token = document.cookie.split(';').find(indice => {
    //       return indice.includes('token=')
    //     })

    //     token = token.split('=')[1]
    //     token = 'Bearer ' + token 

    //     return token
    // }
  }
}
</script>

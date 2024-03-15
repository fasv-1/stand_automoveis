/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';


// importando e configurando vuex (permite criar variáveis globais disponiveis em qualquer componente vue)
import { createStore } from 'vuex'

const store = createStore({
  state() {
    return {
      // teste: 'Teste de recuperação de valor do VueX'
      item: {},
      transacao:{ status:'', menssagem:'' }
    }
  }
})




/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

import Login from './components/Login.vue';
app.component('login-component', Login);

import Home from './components/Home.vue';
app.component('home-component', Home);

import Marcas from './components/Marcas.vue';
app.component('marcas-component', Marcas);

import InputContainer from './components/InputContainer.vue';
app.component('input-container-component', InputContainer);

import Table from './components/Table.vue';
app.component('table-component', Table);

import Card from './components/Card.vue';
app.component('card-component', Card);

import Modal from './components/Modal.vue';
app.component('modal-component', Modal);

import Alert from './components/Alert.vue';
app.component('alert-component', Alert);

import Paginate from './components/Paginate.vue';
app.component('paginate-component', Paginate);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

//---------------Metodo recente da aplicação de filtros globais----------------------------------------------------
//definesse um metodo computado global que pode ser usado em qualquer componente do vue ($filters.funçãoGlobal(parametro a ser filtrado))
app.config.globalProperties.$filters = {
  formataDataTempoGlobal(d){
    if(!d) return ''

  d = d.split('T')

  let data = d[0]
  let tempo = d[1]

  //formatando a data
  data = data.split('-')
  data = data [2] + '/' + data[1] + '/' + data[0]

  //formatar o tempo 
  tempo = tempo.split('.')
  tempo = tempo[0]

  return data + ' ' + tempo
  }
};

//---------------Metodo antigo da aplicação de filtros globais----------------------------------------------------


// Vue.filter('formataDataTempoGlobal', function(d){
//   if(!d) return ''

//   d = d.split('T')

//   let data = d[0]
//   let tempo = d[1]

//   //formatando a data
//   data = data.split('-')
//   data = data [2] + '/' + data[1] + '/' + data[0]

//   //formatar o tempo 
//   tempo = tempo.split('.')
//   tempo = tempo[0]

//   return data + ' ' + tempo
// })

app.use(store)
app.mount('#app');


<template>
  <div :class="estilo" role="alert">
    {{ titulo }}
    <hr>
    <p>{{ detalhes.mensagem }}</p>
    
</div>
</template>

<script>
  export default {
    props:['tipo', 'titulo', 'detalhes'],
    computed:{
      estilo(){
        return 'alert alert-'+ this.tipo;
      }
    }
  }
</script>

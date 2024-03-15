<template>
  <div>
    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col" v-for="t in titulos" :key="key">{{ t }}</th>
          <th v-if="visualizar.visivel || atualizar.visivel || remover.visivel"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="m in dados" :key="m.id">
          <th scope="row">{{ m.id }}</th>
          <td>{{ m.nome }}</td>
          <td><img :src="'/storage/' + m.imagem" alt="imagem da marca" width="30" height="30"></td>
          <td>{{ $filters.formataDataTempoGlobal(m.created_at) }}</td>
          <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel">
            <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm" :data-bs-toggle=visualizar.dataToogle
              :data-bs-target=visualizar.dataTarget @click="setStore(m)">Visualizar</button>
            <button v-if="atualizar.visivel" class="btn btn-outline-primary btn-sm" :data-bs-toggle=atualizar.dataToogle
              :data-bs-target=atualizar.dataTarget @click="setStore(m)">Atualizar</button>
            <button v-if="remover.visivel" class="btn btn-outline-danger btn-sm" :data-bs-toggle=remover.dataToogle
              :data-bs-target=remover.dataTarget @click="setStore(m)">Remover</button>
          </td>

        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: ['dados', 'titulos', 'visualizar', 'atualizar', 'remover'],
  methods: {
    setStore(obj){
      this.$store.state.transacao.status = ''
      this.$store.state.transacao.mesagem = ''
      this.$store.state.item = obj 
    }
  }
}
</script>

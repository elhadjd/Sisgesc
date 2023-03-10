<template>
    <div class="sessao">
      <div class="cabecalho">
        <h2>Lista de Sessões</h2>
      </div>
      <div class="p-0 SessaoLista">
        <div class="d-flex text-secondary titulosSessao">
          <div>Id da sessão</div>
          <div>Ponto de venda</div>
          <div>Responsavel</div>
          <div>Data de abertura</div>
          <div>Valor enformado</div>
          <div>Diferencia</div>
          <div>Data de fecho</div>
          <div>Estado</div>
        </div>

        <div @click="user.nivel != 'Administrador' ? message() : $emit('AbrirCaixa',sessao.id)" v-for="sessao in sessoes" :key="sessao.id" class="d-flex dadosSessao">
            <div><strong>{{"Sessões00"+sessao.id}}</strong></div>
            <div>{{sessao.apelido}}</div>
            <div>{{sessao.apelido}}</div>
            <div>{{sessao.created_at}}</div>
            <div>{{sessao.total_geral_informado}}</div>
            <div :class=" Number(sessao.total_geral_informado) +  Number(sessao.TotalEntrada) - Number(sessao.TotalSaida) - Number(sessao.total_geral) < 0 ? 'text-danger': ''">{{FormatrDinheiro.format(Number(sessao.total_geral_informado) + Number(sessao.TotalSaida) - Number(sessao.TotalEntrada) - Number(sessao.total_geral))}}</div>
            <div>{{sessao.data_de_fecho}}</div>
            <div :class="sessao.estado == 'A abrir' ? 'Abrir': sessao.estado == 'Aberto' ? 'Aberto':'Fechado'">{{sessao.estado}}
                <i :class="sessao.estado == 'A abrir' ? 'fa fa-clock-o': sessao.estado == 'Aberto' ? 'fa fa-opencart':'fa fa-check'"></i>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted,defineProps,ref } from "@vue/runtime-core";
import axios from "axios";
import { useStore } from "vuex";
const store = useStore()
const user = ref(store.state.user)
const emits = defineEmits(['message'])

const props = defineProps({
  dados_caixa:Number
})

const sessoes = ref([]);

const FormatrDinheiro = Intl.NumberFormat('PT-AO',{style:'currency',currency: 'AOA'})

const message = (()=>{
  emits('message','error','Usuario sem aseço');
})

onMounted(() => {
    axios.post("/SessoesCaixa",{id_caixa:props.dados_caixa})
    .then((response) => {
        sessoes.value = response.data.sessoes;
    }).catch((erro) => {
        console.log(erro);
    });
});


</script>

<style scoped lang="scss">
@import "../../../assets/PontoVenda/css/sessoes";
</style>

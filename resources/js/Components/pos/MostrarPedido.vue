<template>
  <div class="Principal">
    <div class="forme">
        <div class="Header">
            <span>{{dados.cliente}}</span>
        </div>
        <div class="Container">
            <div class="Items">
                <div class="Lista" v-for="item, index in dados.lista" :key="index">
                    <div class="nomes">{{item.nome}}</div>
                    <div class="Quantidade">
                        <div>{{item.quantidade+',00Unidades(s)'}}</div>
                        <div>{{FormatarDineiro.format(item.preçovenda)}}</div>
                        <div>{{FormatarDineiro.format(item.total)}}</div>
                    </div>
                </div>
            </div>

            <div class="Total">
                <div class="title">
                    <div>Toatal</div>
                    <div v-for="method,index in dados.methodos" :key="index">
                        <div v-if="method.valor > 0">{{method.nome}}</div>
                    </div>
                </div>
                <div class="valores">
                    <div>{{FormatarDineiro.format(dados.total)}}</div>
                    <div v-for="method,index in dados.methodos" :key="index">
                        <div v-if="method.valor > 0">{{FormatarDineiro.format(method.valor)}}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="Footer">
            <button class="PedirReembolso">Pedir devolução</button>
            <button @click="$emit('fechar')" class="Fechar">Fechar</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted,defineProps } from "@vue/runtime-core";

const FormatarDineiro = new Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

const props = defineProps({
    dados: Object
});

const dados = props.dados

onMounted(()=>{

})
</script>

<style  lang="scss" scoped>
    @import '../../../assets/Pos/css/MostrarPedido.scss';
</style>

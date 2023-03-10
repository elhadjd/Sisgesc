<template>
<div class="Principal">
    <TransitionGroup name="list" tag="ul">
        <MostrarPedido :dados="MostrarLista.dados" @fechar="MostrarLista.estado = false" v-if="MostrarLista.estado"/>
    </TransitionGroup>
    <section class="Header">
        <div>
            <button @click="$emit('NovoPedido')" class="NovoPedido">Nova encomenda</button>
            <button @click="$emit('Fechar')" class="Voltar">Voltar</button>
        </div>
        <div>
            <input type="text" @keyup="Pesquisar" placeholder="Pesquisar...">
        </div>
    </section>
    <section class="Container">
        <div class="Titles">
            <div>Data</div>
            <div>Pedido</div>
            <div>Cliente</div>
            <div>Responsavel</div>
            <div>Total</div>
            <div>Estado</div>
        </div>
        <div class="List">
            <div @mouseover="ViewPedido(ListaOrden.NumPedido)" class="Items" @click="$emit('AlterarPedido',ListaOrden.NumPedido)" v-for="ListaOrden,index in ListaOrdens.slice(0,100)" :key="index">
                <div>{{ListaOrden.data}}</div>
                <div>{{ListaOrden.NumPedido}}</div>
                <div>{{ListaOrden.cliente}}</div>
                <div>{{ListaOrden.user.apelido}}</div>
                <div>{{FormatarDineiro.format(ListaOrden.total)}}</div>
                <div class="ViewPedido">{{ListaOrden.estado}}
                    <Transition>
                    <span @click="ShowList(ListaOrden)" v-if="Mostra == ListaOrden.NumPedido">Mostrar</span>
                    </Transition>
                </div>
            </div>
        </div>
    </section>
</div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import MostrarPedido from '@/components/pos/MostrarPedido.vue';

const FormatarDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

const props = defineProps({session: Number})

const Encomendas = ref()

const ListaOrdens = ref([])

const MostrarLista = ref({
    estado: false,
    dados: null
})

const Pedidos = ref()

const session = ref()

const Mostra = ref(null)

const ViewPedido = ((event)=>{
    Mostra.value = event
})

const ShowList = ((event)=>{
    MostrarLista.value.estado = true;
    MostrarLista.value.dados = event
})

const OnMounted = onMounted(() => {
    session.value = localStorage.getItem('session')
    Encomendas.value = localStorage.getItem('Encomendas'+session.value);
    ListaOrdens.value = JSON.parse(Encomendas.value);
    ListaOrdens.value.reverse()
})


</script>

<style  lang="scss" scoped>
    @import '../../../assets/Pos/css/ListePedido.scss';
</style>


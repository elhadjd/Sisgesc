<template>
<Toast/>
    <div class="mt-0" style="height:100vh;width: 100vw">
        <Headers @modulos="modulos" :menus="menus"/>
        <div class="Container">
            <Caixa v-if="modul == 'caixa'" :caixaId="session" @message="message"/>
            <Relatorio v-if="modul == 'Relatório'"/>
            <Gastos v-if="modul == 'Gastos'"/>
            <AvaliarStock v-if="modul == 'Avaliação de Stock'"/>
            <Orden v-if="modul == 'Orden de Venda'"/>
            <Menu
            @definicaoCaixa="definicaoCaixa"
            @Caixa="EmitsMenu"
            @sessao="AbrirSessao"
            @CriarCaixa="CriarCaixa"
            @AbrirCaixa="AbrirCaixa"
            @message="message"
            v-if="modul == 'Ponto de venda'"
            :dados="data.dados"
            />

            <Fornecedores v-if="modul == 'fornecedor'"/>

            <Produtos v-if="modul == 'Artigos'"/>
            <Sessoes
            v-if="modul == 'sessao'"
            :dados_caixa="dados_caixa"
            @AbrirCaixa="AbrirCaixa"
            @message="message"
            />
            <NovaCaixa @message="message" @close="modulos('menu')" :DadosCaixa="DadosCaixa" v-if="modul == 'NovaCaixa'"/>
        </div>
    </div>
</template>

<script setup>
import Headers from '../layouts/Headers.vue'
import Fornecedores from '../components/fornecedores/index.vue'
import Sessoes from '@/components/PontoDeVenda/sessoes.vue';
import Index from '@/layouts/index.vue'
import { onMounted, reactive ,ref} from '@vue/runtime-core';
import Menu from '@/components/PontoDeVenda/menu.vue';
import Produtos from '@/components/produtos/produtos.vue'
import Orden from '@/components/PontoDeVenda/Orden.vue'
import Gastos from '@/components/PontoDeVenda/gastos.vue'
import { useStore } from 'vuex';
import AvaliarStock from '@/components/PontoDeVenda/AvaliarStock.vue';
import Relatorio from '@/components/PontoDeVenda/relatorio/index.vue';
import Caixa from '@/components/PontoDeVenda/caixa.vue';
import NovaCaixa from '@/components/PontoDeVenda/CashConfig/NovaCaixa.vue';
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast";
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';

const menus = ref([
    {"menu": "Ponto de venda"},
    {"menu":"Relatório",
        "subMenu": [
            {"name":"Orden de Venda"},
            {"name":"Relatório"},
            {"name":"Gastos"},
            {"name":"Avaliação de Stock"}
        ]
    },
    {"menu": "Artigos"},
])

const props = defineProps({
    data: Object
});
const toast = useToast()
const modul = ref('Ponto de venda')
const store = useStore()
const session = ref()

const dados_caixa = ref();
const data = reactive({
    Apps:null,
    dados: {
        caixas: props.data,
        user:store.state.user,
    }
});

const DadosCaixa = ref()

const definicaoCaixa = ((payload) =>{
    DadosCaixa.value = payload;
    modul.value = 'NovaCaixa'
})

const modulos = ((event)=>{
    store.state.EstadoMuvemento.estado = false
    store.state.ProdutosMostrarUm = false
    MostrarDrop.value = null
    modul.value = event
})

let MostrarDrop = ref(null)
const OnMounted = onMounted(()=>{
    data.Apps = props.app
});

const Sair = (()=>{
    Inertia.post('/sair');
})

const message = ((tipo,message)=>{
    toast.add({
        severity: tipo,
        summary: 'Message',
        detail: message,
        life: 5000
    })
})

const DropShow = ((evento)=>{
    if (evento == MostrarDrop.value) {
        MostrarDrop.value = null
    } else {
        MostrarDrop.value = evento
    }
})

const EmitsMenu = ((event,OutroEvento)=>{
    if (event == 'AbrirCaixa') {
        axios.get('/FormularioSession?id_caixa='+OutroEvento)
        .then((Response) => {
            session.value = Response.data.session.id
            modul.value = 'caixa'
        }).catch((err) => {
            console.log(err);
        });
    }
})

const CriarCaixa = (()=>{
    modul.value = 'NovaCaixa';
})

const AbrirSessao = (payload) => {
modul.value = "sessao"
dados_caixa.value = payload
}

const AbrirCaixa = (payload) => {
    modul.value = "caixa"
    session.value = payload
}
</script>

<style scoped lang="scss">
@import '../../assets/PontoVenda/css/PontoVenda';
</style>

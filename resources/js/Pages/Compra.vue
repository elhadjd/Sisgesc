<template>
<div class="mt-0" style="height:100vh;width: 100vw">
    <Toast/>
    <Headers @modulos="modulos" :menus="menus"/>
    <div class="Container">
        <Produtos @message="message" v-if="modulo == 'Artigos'" />
        <NovaCompra @fechar="modulo = 'Compras'" @message="message" v-if="modulo == 'compra'" :Encomenda="Encomenda"/>
        <Menu @message="message" @modulo="modulos" v-if="modulo == 'Compras'"/>
        <Fornecedor v-if="modulo == 'Fornecedores'"/>
        <Payments v-if="modulo == 'payments'"/>
    </div>
</div>

</template>

<script setup>
import { onMounted,ref,watch } from '@vue/runtime-core';
import Menu from '../components/Compras/menu.vue'
import Produtos from '../components/produtos/produtos.vue'
import NovaCompra from '../components/Compras/NovaCompra.vue'
import useEventsBus from '@/Eventbus';
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast';
import { useStore } from 'vuex';
import { Inertia } from '@inertiajs/inertia';
import Payments from '../components/Compras/Payments.vue'
import Fornecedor from '../components/fornecedores/index.vue'
import Headers from '../layouts/Headers.vue'

const menus = ref([
    {"menu": "Compras"},
    {"menu": "Fornecedores",
        "subMenu": [
            {"name":"Fornecedores"},
            {"name":"Pagamentos"}
        ]
    },

    {"menu":"Relatório",
        "subMenu": [
            {"name":"Orden de compra"},
            {"name":"Relatorio"},
            {"name":"Pagamentos"},
            {"name":"Orden"}
        ]
    },
    {"menu": "Artigos"},
])

const toast = useToast()
const {bus} = useEventsBus();
const store = useStore()
const modulo = ref('Compras');
const MostrarDrop = ref(null)
const Mostrar = ref(null)

const MostrarTitls = (event)=>{
    if (event == Mostrar.value) {
        Mostrar.value = null
    } else {
        Mostrar.value = event
    }
}

const MostrarTitl = (event)=>{
    if (Mostrar.value != null) {
        Mostrar.value = event
    }
}

const props = defineProps({
    user:Object,
    app:Object
});

const DropShow = ((evento)=>{
    if (evento == MostrarDrop.value) {
        MostrarDrop.value = null
    } else {
        MostrarDrop.value = evento
    }
})

const Sair = (()=>{
    Inertia.post('/sair');
})

watch(()=>bus.value.get('CompraPedidoFormulario'), (payload) => {
   store.state.EncomendaCompra = payload[0]
   modulo.value = 'compra'
});

const modulos = ((moduloEncomenda,DadosEncomenda)=>{
    modulo.value = moduloEncomenda
    store.state.EncomendaCompra = DadosEncomenda
    Mostrar.value = null
})

const message = ((message,tipo)=>{
    toast.add({
        severity: tipo,
        summary: 'Message',
        detail: message,
        life:5000
    })
})

</script>

<style lang="scss" scoped>
@import "../../assets/Compra/css/compra";
</style>

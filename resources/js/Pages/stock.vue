<template>
    <div class="mt-0" style="height:100vh;width: 100vw">
        <Toast/>
        <Headers @modulos="modulos" :menus="menus"/>
        <div class="Container">
            <Produtos v-if="modul == 'Artigos'"/>
            <MenuStock v-if="modul == 'Stock'"/>
            <CatalogProduct v-if="modul == 'Catolologo'"/>
            <Armagens @message="message" v-if="modul == 'Armagens'"/>
            <Transferencias  @message="message" @handleModule="modulos" v-if="modul == 'Transferencias'"/>
            <NewTransfer @modulo="modulos" :transfer="transfer"  @message="message" v-if="modul == 'transfer'"/>
        </div>
    </div>
</template>

<script setup>
import Headers from '../layouts/Headers.vue'
import CatalogProduct from '../components/stock/catalogProduct.vue'
import Index from '@/layouts/index.vue'
import { ref } from '@vue/runtime-core';
import Produtos from '@/components/produtos/produtos.vue'
import MenuStock from '@/components/stock/MenuStock.vue'
import Armagens from '../components/stock/armagens.vue'
import NewTransfer from '../components/stock/transfers/newTransfer.vue';
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast';
import Transferencias from '../components/stock/transfers/transfers.vue'
const modul = ref("Stock")
const MostrarDrop = ref()
const toast = useToast()

const menus = ref([
    {"menu": "Stock"},
    {"menu":"Relatório",
        "subMenu": [
            {"name":"Catolologo"},
            {"name":"Transferencias"},
        ],
    },
    {"menu": "Armagens"},
    {"menu": "Artigos"},
])

const transfer = ref([])

const modulos = ((event,Transfer=null)=>{
    if (event == "transfer") {
        transfer.value = Transfer
    }
    MostrarDrop.value = null
    modul.value = event
})

const DropShow = ((evento)=>{
    if (evento == MostrarDrop.value) {
        MostrarDrop.value = null
    } else {
        MostrarDrop.value = evento
    }
})

const message = ((message,type)=>{
    toast.add({
        severity: type,
        summary: 'Message',
        detail: message,
        life: 5000,
    })
})
</script>

<style scoped lang="scss">
.Container{
    width: 100%;
    height: 92%;
    box-sizing: border-box;
}
@import '../../assets/stock/scss/menuStock';
</style>

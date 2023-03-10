<template>
    <div class="principal">
        <Recibo v-if="estateModalRecibo" @closeModal="estateModalRecibo = false" :encomenda="order"/>
        <div class="Header">
            <h1>Transferencia</h1>
        </div>
        <div class="Principal">
            <div class="Container">
                <div class="Botoes">
                    <button v-if="order.estado == 'Cotação'" @click="saveTransfer" class="botoes">Guardar</button>
                    <button @click="estateModalRecibo = true" class="botoes">Imprimir</button>
                    <button @click="AnnularTransfer" v-if="order.estado != 'Cotação' && order.estado != 'Annulado'" class="discartar">Annular a transferencia</button>
                    <button @click="$emit('modulo','Transferencias')" class="discartar">Fechar</button>
                </div>
                <div class="Formulario">
                    <div class="Form">
                        <div class="HeaderFatura">
                            <div class="NumeroOrdem">
                                <h1>Transferencias/00{{order.id}}</h1>
                            </div>
                            <div class="Informacao">
                                <div>
                                    <div class="cliente">
                                        <div>
                                            <button :disabled="order.estado != 'Cotação'" @click="selectLocalDestine" id="in" class="dropdown-toggle">
                                                {{order.armagens_destination_id==null ? 'selectione Loja':order.NomeArmagen}}
                                            </button>
                                        </div>
                                        <div v-if="listLocals" class="shadow ListCliente">
                                            <div @click="addLocalDestine(item)" class="clienteRows" v-for="item in armagens" :key="item.id">
                                                {{item.NomeArmagen}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <!-- <DataEncomenda/> -->
                                </div>
                            </div>
                        </div>
                        <div class="FooterFatura">
                            <ListTransfer @message="message" :transfer="order.id"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import useEventsBus from '@/Eventbus'
import { onMounted, ref } from '@vue/runtime-core'
import axios from 'axios'
import DataEncomenda from '../../Compras/DataEncomenda.vue'
import ListTransfer from './listTransfer.vue'
import Recibo from './recibo.vue'
const props = defineProps({
    transfer: Object,
})

const {emit} = useEventsBus()
const estateModalRecibo = ref(false)
const listLocals = ref(false)
const order = ref(props.transfer)
const emits = defineEmits(['message'])
const armagens = ref([])
const message = ((message,type)=>{
    emits('message',message,type)
})
onMounted(()=>{
    axios.get('/getArmagens','getArmagens')
    .then((response) => {
        armagens.value = response.data
    }).catch((err) => {
        console.log(err);
    });
})

const selectLocalDestine = (()=>{
    listLocals.value = !listLocals.value
})

const addLocalDestine = ((local)=>{
    axios.post(`/addLocalDestine/${order.value.id}/${local.id}`)
    .then((response) => {
        order.value.NomeArmagen = local.NomeArmagen
        order.value.armagens_destination_id  = local.id
        listLocals.value = !listLocals.value
    }).catch((err) => {
        console.log(err);
    });
})

const saveTransfer = (()=>{
    if (order.value.armagens_destination_id  == null) return message('Por favor seleciona o local de destino desta transferencia !!!','info')
    axios.post(`/saveTransfer/${order.value.id}`)
    .then((response) => {
        if (response.data.message) return message(response.data.message,response.data.type) 
        order.value = response.data
        emit('saveTransfer',response.data);
    }).catch((err) => {
        console.log(err);
    });
})

const AnnularTransfer = (()=>{
    axios.post(`/AnnularTransfer/${order.value.id}`)
    .then((response) => {
        if (response.data.message) return message(response.data.message,response.data.type) 
        order.value = response.data
        emit('saveTransfer',response.data);
    }).catch((err) => {
        console.log(err);
    });
})


</script>

<style scoped lang="scss">
.Container{
    height: 100% !important;
    
}
@import '../../../../assets/stock/scss/newTransfer.scss';
</style>

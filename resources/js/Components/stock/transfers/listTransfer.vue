<template>
  <div class="h-100 w-100">
    <div class="Container">
        <div class="d-flex TitlsPedido">
            <div class="w-25" style="text-align: left">Produto</div>
            <div>Quantidade</div>
            <div>Preço</div>
            <div>Gastos</div>
            <div>Preço final</div>
            <div>Total</div>
            <div>Ação</div>
        </div>
        <div class="w-100">
            <div v-for="item in order.itemTransfer" :key="item.id" class="ListaPedidos" >
                <input type="text" class="w-25 text-start" v-model="item.nome" readonly/>

                <input type="text"
                :class="'quantidade'+item.id"
                @click="SelectText(item.id,'quantidade')"
                v-model="item.quantity"
                :disabled="order.order.estado != 'Cotação'"
                @keyup.enter="UpdateItem(item)"
                id="inputRef" ref="inputRef"
                placeholder="Quantidade"/>

                <input placeholder="Preço"
                :class="'Preço'+item.id"
                :disabled="order.order.estado != 'Cotação'"
                @keyup.enter="UpdateItem(item)"
                @click="SelectText(item.id,'Preço')"
                v-model="item.price"
                 id="inputRef" ref="inputRef" type="text"/>

                <input placeholder="Gasto"
                @click="SelectText(item.id,'spend')"
                @keyup.enter="UpdateItem(item)"
                :disabled="order.order.estado != 'Cotação'"
                v-model="item.spend"
                :class="'spend'+item.id" type="text"/>

                <input placeholder="Preço final"
                :value="formatDinheiro.format(item.final_price)"
                :class="'final_price'+item.id" required disabled type="text"/>

                <input type="text" :value="formatDinheiro.format(item.total)" required disabled/>

                <div>
                    <!-- <Caixa class="icone" @click="Addcaixa(item)"/> -->
                    <i  v-if="order.order.estado == 'Cotação'" @click="DeleteItem(props.transfer,item.id)" class="fa fa-trash"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="ResultadoTotal border-top bg-white d-flex">
        <div class="w-50">
            <div v-if="order.order.estado == 'Cotação'">
                <input id="Produto"
                placeholder="Selectionar produto"
                v-model="products.name"
                @click="selectProducts('')"
                @keyup="(e)=>searchProduct(e.target.value)"
                class="w-75 p-1 border-bottom" />
                <div v-if="products.state" class=" text-secondary rounded-bottom bg-white ListaProdutos overflow-auto w-75 border border-top-0">
                    <div v-for="product in products.list" :key="product.id"
                    @click="selectProduct(product)"
                    id="ListaProdutos" class="d-flex">
                       {{product.nome}}
                        <div v-if="Number(product.qtd <=0)" class="StockEsgatado">
                            <i class="fa fa-info-circle text-danger" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="BotoesCriar">
                        <button @click="NewProduct">Criar </button>
                        <button @click="CreateUpdateProduct">Criar e editar </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-50">
            <div class="d-flex">
                <div class="TitlTotals">
                    <div>Total da mercadoria :</div>
                    <div>Total de Gastos :</div>
                </div>

                <div class="Totals">
                    <div>{{formatDinheiro.format(order.order.total - order.order.total_gastos)}}</div>
                    <div>{{formatDinheiro.format(order.order.total_gastos)}}</div>
                </div>
            </div>

            <div class="w-100 mt-3 d-flex border-top">
                <div class="TitlTotals">Total da fatura :</div>
                <div class="Totals">
                    {{formatDinheiro.format(order.order.total)}}
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import useEventsBus from '@/Eventbus';
import { onMounted, ref, watch } from '@vue/runtime-core';
import axios from 'axios';
import Caixa from 'vue-material-design-icons/PackageVariantClosedPlus.vue'
const formatDinheiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'});
const props = defineProps({
    transfer: Number
})
const emits = defineEmits(['message','progress','order'])
const {bus} = useEventsBus()
const order = ref({
  itemTransfer: [],
  order: [],
})

const message = ((message,type)=>{
    emits('message',message,type)
})

const products = ref({
    name: null,
    state: false,
    list: [],
    storeList: [],
})

const produto = ref({
    nome:null,
    estado:false,
    lista: [],
    StorProduct: []
});

onMounted(()=>{
    getListTransfer()
})

const getListTransfer = (()=>{
    axios.get(`/selectOrder/${props.transfer}`)
    .then((response) => {
        order.value.itemTransfer = response.data.list
        order.value.order = response.data.order
    }).catch((err) => {
        console.log(err);
    });
})

watch(()=>bus.value.get('saveTransfer'),(orders)=>{
    getListTransfer()
})

const selectProducts = (dados) => {
    axios.post('BuscarProdutus',{nome:dados}).then((response)=>{
        products.value.list = response.data
        products.value.storeList = response.data
        products.value.state = true
    })
}

const selectProduct = (event) => {
    products.value.state = false
    event.idOrder = props.transfer
    addProduct('transfers/addProduct',event)
}

const addProduct = (rota,dados) => {
    axios.post(`/${rota}`,{dados:dados}).then((response)=>{
        if (response.data.message) return message(response.data.message,response.data.type)
        order.value.itemTransfer = response.data.list
        order.value.order = response.data.order
    })
}

const DeleteItem = ((idOrder,id)=>{
    axios.delete(`/delete_item_transfer/${id}/${idOrder}`)
    .then((response) => {
        order.value.itemTransfer = response.data.list
        order.value.order = response.data.order
    }).catch((err) => {
        console.log(err);
    });
})

const SelectText = ((id,tipo)=>{
    inputRef.value
    var classe = tipo+id
    document.querySelector('.'+classe).select();
})


const UpdateItem = ((Item)=>{
    let id = Item.id
    Item.nome = undefined
    Item.idOrder = undefined
    Item.id = undefined
    return addProduct(`UpdateItemsTransfer/${id}`,Item);
})


const searchProduct = ((nameProd)=>{
    const filter = products.value.storeList.filter((product)=>{
        return String(product.nome).toLowerCase().includes(String(nameProd).toLowerCase())
    })
    products.value.list = filter
})
</script>

<style lang="scss" scoped>
@import "../../../../assets/faturacao/css/FaturacaoPedido.scss";
</style>

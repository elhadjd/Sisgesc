<template>
<Progress v-if="$store.state.StateProgress"/>
  <div class="MenuProdutos">
    <div v-if="FormSingleProduct.state" class="novo_prod w-100 h-100">
        <Produto :produto="FormSingleProduct.product" @descartar="OnMounted"/>
    </div>

    <div v-else class="w-100 h-100 lista_dos_produtos">
        <div class="MenuCimaProdutos">
            <div>
                <span class="mx-3 h-25 text-secondary"><strong>Lista de artigos</strong></span><br>
                <button @click="CriarProduto" class="btncriar_artigo mt-2 ms-2">Novo produto</button>
            </div>
            <div class="div_da_pesquisa_prod">
                <span class="p-input-icon-right w-100">
                    <i class="pi pi-search" />
                    <input type="search" v-model="form.nome" name="pesqusar_prod" id="pesqusar_prod"
                    placeholder="Digite nome ou preço do artigo" @keyup="Pesquisar">
                </span>
            </div>
        </div>
        <div class="border-0 border-top w-100 lista_dos_artigose overflow-auto">
            <label @click="MostrarProd(produto)" v-for="produto in Products.Products" :key="produto.id" class="mx-1 mb-2 bloco_artigo" id="bloco_artigo">
                <div class="d-flex">
                    <div id="div_da_imagem"><img :src="'/produtos/image/'+produto.imagem" alt="" class="rounded float-right"></div>
                    <div class="div_preco_qtd text-secondary">
                        <div class="NomeProduto"><strong>{{produto.nome}}</strong></div>
                        <div class="d-flex">
                            <div class="preco_qtd">
                                <div><strong>Preço :</strong> {{FormetDineiro.format(produto.preçovenda)}} </div>
                                <div><strong>Stock :</strong> {{produto.qtd+',00Un(s)'}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="strela"><i class="fa fa-star-o text-warning"></i></div>
                </div>
            </label>
        </div>
    </div>
</div>
</template>

<script setup>
import { onMounted, ref,onUpdated, reactive } from '@vue/runtime-core';
import axios from 'axios';
import { useForm } from '@inertiajs/inertia'
import Produto from './produto.vue';
import { useStore } from 'vuex';
import Progress from '@/components/confirmation/progress.vue'
const FormetDineiro = new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA',})
const store = useStore()
const FormSingleProduct = reactive({
    state: false,
    product: [],
});
const Products = reactive({
    Products: [],
    StoreProducts: []
})
const form = reactive({
    colun1: 'nome',
    colun2: 'preçovenda',
    table: 'produtos',
    nome: null,
    estado: 0
})

const MostrarProd = ((event)=>{
    FormSingleProduct.product = event
    FormSingleProduct.state = true
})
const OnMounted = onMounted(() => {
    store.state.StateProgress = true
    axios.get('/ProdutosMod')
    .then((Response) => {
        Products.Products = Response.data.produtoss;
        Products.StoreProducts = Response.data.produtoss;
        store.state.StateProgress = false
        FormSingleProduct.state = false
        if (form.nome != null) return Pesquisar()
    }).catch((err) => {
        store.state.StateProgress = false
        FormSingleProduct.state = false
        console.log(err);
    });
})

const CriarProduto = (()=>{
    store.state.StateProgress = true
    axios.get('/novo_produto')
    .then((Response) => {
        FormSingleProduct.state = true
        store.state.StateProgress = false
        FormSingleProduct.product = Response.data.produto
    })
})

const Pesquisar = ((event)=>{
    // a filtra a veriavel resultado
    const filtrar = Products.StoreProducts.filter(object=>{
        if (Number(form.nome)) {
            return String(object.preçovenda).toLowerCase().includes(form.nome.toLowerCase())
        } else {
            return String(object.nome).toLowerCase().includes(form.nome.toLowerCase())
        }
    })
    Products.Products  = filtrar;
})

</script>

<style scoped lang="scss">
@import url('../../../assets/produtos/css/menu.scss');
</style>

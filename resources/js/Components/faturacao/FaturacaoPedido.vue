<template>
<Addbox :artigo="AddBox.artigo" @Close="AddBox.state = false" @Confirmou="BoxConfirm" v-if="AddBox.state"/>
<div class="h-100 w-100">
    <div class="Container">
        <div class="d-flex TitlsPedido">
            <div class="w-25" style="text-align: left">Produto</div>
            <div>Quantidade</div>
            <div>Preço</div>
            <div>Desconto</div>
            <div>Total</div>
            <div>Ação</div>
        </div>
        <div class="w-100">
            <div class="ListaPedidos" v-for="prodFaturaca in Encomenda.ItemInvoice" :key="prodFaturaca.id">
                <input type="text" class="w-25 text-start" readonly :value="prodFaturaca.nome">
                <input @keyup.enter="UpdateItem(prodFaturaca)" @click="Alteracao(prodFaturaca.IdProd, 'quantidade')"
                    v-model="prodFaturaca.quantidade"
                    type="text"
                    :disabled="Encomenda.Invoice.estado.trim() !='Cotação'"
                    placeholder="Quantidade"
                    :class="'quantidade'+prodFaturaca.IdProd"/>

                <input
                placeholder="Preço"
                type="text"
                :disabled="Encomenda.Invoice.estado.trim() !='Cotação'"
                v-model="prodFaturaca.PrecoVenda"
                @keyup.enter="UpdateItem(prodFaturaca)"
                @click="Alteracao(prodFaturaca.IdProd, 'PrecoVenda')"
                :class="'PrecoVenda'+prodFaturaca.IdProd"/>

                <input  @keyup.enter="UpdateItem(prodFaturaca)" @click="Alteracao(prodFaturaca.IdProd, 'Desconto')"
                v-model="prodFaturaca.Desconto"
                :class="'Desconto'+prodFaturaca.IdProd"
                :disabled="Encomenda.Invoice.estado.trim() !='Cotação'"
                placeholder="Desconto" type="text" />

                <input
                @click="Alteracao(prodFaturaca.IdProd, 'quantidade')"
                placeholder="Total"
                :value="FrmatDinheiro.format(prodFaturaca.TotalVenda)"
                type="text" required disabled />
                <div>
                    <Caixa  v-if="Encomenda.Invoice.estado.trim() =='Cotação'" class="icone" @click="Addcaixa(prodFaturaca)"/>
                    <i @click="DeleteProduct(prodFaturaca.id)"  v-if="Encomenda.Invoice.estado.trim() =='Cotação'" class="fa fa-trash"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="ResultadoTotal border-top bg-white d-flex">
        <div class="w-50">
            <div v-if="Encomenda.Invoice.estado.trim() == 'Cotação'">
                <input @click="ShowProducts" @keyup="SearchProduct" id="Produto" placeholder="Selectionar produto" class="w-75 p-1 border-bottom" />
                <div v-show="Produtos.ShowProd" class=" text-secondary rounded-bottom
                    bg-white ListaProdutos overflow-auto w-75 border border-top-0 ">
                    <div v-for="produto in Produtos.ListProducts" @click="AddProduct(produto)" :key="produto.id"
                        id="ListaProdutos" class="d-flex" >
                        {{ produto.nome }}
                        <div v-if="produto.qtd <= 0" class="StockEsgatado">
                            <i class="fa fa-info-circle text-danger" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-50">

            <div class=" d-flex">
                <div class="TitlTotals">
                    <div>Total da mercadoria : </div>
                    <div>Total do disconto : </div>
                </div>

                <div class="Totals">
                    <div>{{FrmatDinheiro.format(Encomenda.Invoice.TotalMercadoria)}} </div>
                    <div>{{FrmatDinheiro.format(Encomenda.Invoice.desconto)}}</div>
                </div>
            </div>

            <div class="w-100 mt-3 d-flex border-top">
                <div class="TitlTotals">Total da fatura : </div>
                <div class="Totals">{{FrmatDinheiro.format(Encomenda.Invoice.TotalFatura)}}</div>
            </div>

        </div>
    </div>
</div>
</template>

<script setup>
import Addbox from '../Compras/AddBox.vue'
import { onMounted ,reactive,ref, watch} from "@vue/runtime-core"
import axios from "axios"
import Caixa from 'vue-material-design-icons/PackageVariantClosedPlus.vue'
import useEventsBus from '@/Eventbus'

const {bus} = useEventsBus()

const FrmatDinheiro = Intl.NumberFormat('PT-AO',{
    style: 'currency', currency: 'AOA'
})

const AddBox = ref({
    state: false,
    gasto: false,
    artigo: []
})
const props = defineProps({
    Invoice: Object
})
const emits = defineEmits(['message'])

const Produtos = ref({
    ShowProd: false,
    ListProducts: [],
    StoreProducts: []
})

const Encomenda = ref({
    Invoice: props.Invoice,
    ItemInvoice: []
})

const OnMounted = onMounted(async ()=>{
    return GetData(`/BuscarEncomendaFaturacao/${props.Invoice.id}`)
})

const ShowProducts = (()=>{
    axios.post("/BuscarProdutus").then((Response) => {
        Produtos.value.ShowProd = true
        Produtos.value.ListProducts = Response.data;
        Produtos.value.StoreProducts = Response.data;
    });
})

const GetData = (async (rota)=>{
    await axios.get(rota)
    .then((Response) => {
        Encomenda.value = Response.data
    })
})

const SearchProduct = ((event)=>{
    const FilterSearch = Produtos.value.StoreProducts.filter(object=>{
        return object.nome.toLowerCase().includes(event.target.value.toLowerCase())
    })
    if (FilterSearch.length > 0) {
        Produtos.value.ListProducts = FilterSearch
    } else {
        emits('message','Nenhum produto encontrado com este nome','info')
    }
})

const AddProduct = ((product)=>{
    if (Number(product.preçovenda)<Number(product.preçocust)) {
        emits('message','O preço deste produto é invalido','info')
    } else {
        if (Number(product.qtd)<=0) {
            emits('message','A quantidade deste produto não e suficiente ','info')
        } else {
            axios.post(`/AdicionarProdutos`,{product:product,Invoice: Encomenda.value.Invoice})
            .then((Response) => {
                if (Response.data.message) {
                    emits('message',Response.data.message,'info')
                } else {
                    Encomenda.value = Response.data
                }
            }).catch((err) => {
                console.log(err);
            });
        }
    }
})

const DeleteProduct = ((product)=>{
    if (Encomenda.value.Invoice.estado.trim() == 'Cotação') {
        axios.delete(`/apagarLinhaFaturacao/${Encomenda.value.Invoice.id}/${product}`)
        .then((Response) => {
            Encomenda.value = Response.data
        }).catch((err) => {
            console.log(err);
        });
    }
})

const UpdateItem = ((Rows)=>{
    if (Encomenda.value.Invoice.estado.trim() == 'Cotação') {
        if (Number(Rows.quantidade) > Number(Rows.qtd))  return emits('message',`Atenção a quantidade fornecida não é suficiente`,'info')
        if (Rows.PrecoCusto >= Number(Rows.PrecoVenda)) emits('message',`Atenção o preço fornecido não e valido`,'info')
        axios.post(`/UpdateRows/${Encomenda.value.Invoice.id}`,{dados:Rows})
        .then((Response) => {
            Encomenda.value = Response.data
        }).catch((err) => {
            console.log(err);
        });
    }
})

const Addcaixa = ((artigo)=>{
    AddBox.value.artigo = artigo
    AddBox.value.state = true
})

const BoxConfirm = ((dados)=>{
    const Rows = Encomenda.value.ItemInvoice.filter(item=> item.id === dados.id)
    Rows[0].quantidade = dados.QuatidadeFinal
    Rows[0].PrecoVenda = dados.PrecoUnidade
    AddBox.value.state = false
    return UpdateItem(Rows[0])
})

 const Alteracao = (id, tipo)=> {
    if (Encomenda.value.Invoice.estado.trim() == 'Cotação') {
        // Nomear o nome das classes
        var classe = tipo+id
        document.querySelector('.'+classe).select();
    } else {
        emits('message','Atenção esta orden não pode ser alterado por estar confirmado clica em editar para poder fazer alteração','info')
        return false;
    }
}

watch(()=>bus.value.get('Invoice'), (Invoice)=>{
    Encomenda.value.Invoice = Invoice[0]
})
</script>

<style scoped lang="scss">
@import "../../../assets/faturacao/css/FaturacaoPedido.scss";
</style>

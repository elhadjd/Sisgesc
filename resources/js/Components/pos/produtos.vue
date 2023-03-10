<template>
<Transition name="bounce">
<InfoProduct v-if="OpenInfo" :Information="Information" @CloseInfo="OpenInfo = false"/>
</Transition>
  <div class="Artigos">
    <label
      :class="idProd == produto.id ? { shake: disabled } : ''"
      v-for="produto in produtos.slice(0,50)"
      :key="produto.id"
      class="formArtigo">
      <div style="height: 122px; width: 125px; padding: 5px ,5px,5px,5px">
        <div>
          <div class="PrecoProd">
            <div class="iconesBloco">
                <i class="fa fa-exclamation-circle" style="color: red; margin-right: 5px" v-if="produto.qtd <= 0" aria-hidden="true"></i>
                <i class="fa fa-info-circle" @click="ShwInfo(produto)"></i>
            </div>
            <div id="ProdutoPreco">{{ FormatarDineiro.format(produto.preçovenda) }}</div>
          </div>
          <div class="ProdutoImagem">
            <img
            @click="AddProd(produto)"
              :src="'/produtos/image/' + produto.imagem"
              :alt="produto.imagen"
              srcset=""
            />
          </div>
          <div class="ProdutoNome">{{ produto.nome }}</div>
        </div>
      </div>
    </label>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from "@vue/runtime-core"
import { useToast } from "primevue/usetoast";
import {mapMutations,mapState, useStore} from "vuex"
import Toast from 'primevue/toast'
import InfoProduct from './InfoProduct.vue'
import axios from "axios";
const produtos = ref([]);
const detalhados = ref()
const Lista_de_preco = ref()
const toast = useToast()
const Information = ref();
const disabled = ref(false)
const idProd = ref()
const OpenInfo = ref()
const store = useStore();
const emits = defineEmits(['AddProds','message']);

const FormatarDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: "AOA"})

const PesquisarProduto = computed(()=>{
    return store.state.PesquisarProduto
})

const ShwInfo = ((event)=>{
    Information.value = event
    OpenInfo.value = true
})

const OnMounted = onMounted(async ()=>{
    await axios.get('/trager_produtos')
    .then((Response)=>{

        localStorage.setItem('produtos',JSON.stringify(Response.data.produtos));
        localStorage.setItem('detalhados',JSON.stringify(Response.data.detalheados));
        localStorage.setItem('Lista_de_preco',JSON.stringify(Response.data.Lista_de_preco));

        detalhados.value = Response.data.detalheados
        produtos.value = Response.data.produtos
        Lista_de_preco.value = Response.data.Lista_de_preco
    })
})

const AddProd = ((produto) =>{

    store.state.detalhados = null
    // A selectionar os artigos detalheado
    const detalhados = JSON.parse(localStorage.getItem('detalhados'));
    if (Number(produto.preçovenda) >= produto.preçocust) {
        //A verificar se o artigo clicado esta detalhado
        const detalho = detalhados.find(o => o.idArtigodetalhado === produto.id)
        if (detalho) {
            // A verificar se a quntidade de artigo pai e suficiente
            var artigo_pai = prod.find(o => o.IdArtigo === detalho.idArtigoPrincipal)
            if (artigo_pai.qtd <= 0) {
                store.state.messagen = 'Atenção a quantidade deste produto ja não e suficiente !!!'
                store.state.tipo_menssagen = 'error'
            } else {
                axios.get('/AdicionarProduto?'+'quantidade='+1+'&IdProd='+artigo.IdArtigo)
                .then((Response)=>{
                    // A verificar se nao tem nenhum problema na requisição
                    if (Response.data.message==='successo') {
                        store.state.detalhados = detalho
                        this.ADD_PRODUCT(produto)
                    }else{
                        store.state.messagen = (Response.data.message);
                        store.state.tipo_menssagen = Response.data.tipo
                    }
                })
            }
        } else {
            // A verificar se este produto tem quantidade suficiente em stock
            if (produto.qtd <=0) {
                idProd.value = produto.id
                disabled.value = true
                setTimeout(() => {
                    disabled.value = false
                }, 1500)
                emits('message','error','Atenção a quantidade deste produto ja não e suficiente !!!')
            } else {
                emits('AddProds',produto)
            }
        }
    } else {
        emits('message','error','Atenção o preço de venda não pode ser menor do que preço de custo !!!')
    }


})

const ProdutosPesquisa = ref([])

watch(PesquisarProduto , (valor)=>{
    ProdutosPesquisa.value = JSON.parse(localStorage.getItem('produtos'));
    if (valor != null || valor != '') {
        const prods = ProdutosPesquisa.value.filter(object=>{
            if (valor == object.codego) {
                return String(valor).toLowerCase() == object.codego
            }else {
                return String(object.nome).toLowerCase().includes(valor.toLowerCase(),0)
                ||  String(object.preçovenda).toLowerCase().includes(valor.toLowerCase(),0)
            }
        })
        if (prods.length == 1) {
            store.state.PesquisarProduto = null
            AddProd(prods[0])
        } else if(prods.length >= 1){
            produtos.value = prods
        }else if(prods.length == 0){
            emits('message','info','Nemhum produto encontrado com este nome ')
        }
    }
})
</script>

<style lang="scss" scoped>
@import '../../../assets/Pos/css/produtos';
</style>

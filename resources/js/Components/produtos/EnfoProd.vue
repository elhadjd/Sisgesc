<template>
  <div class="BottomProd">
    <Progress v-if="$store.state.StateProgress" />
    <div class="botoes_do_meio">
      <div
        :class="props.produto.tipo == 'precos' ? 'core' : ''"
        @click="Tipoinfos('precos')"
      >
        Informação
      </div>
      <div
        :class="props.produto.tipo == 'fornecedore' ? 'core' : ''"
        @click="Tipoinfos('fornecedore')"
      >
        Fornecedore
      </div>
      <div
        :class="props.produto.tipo == 'ListaPreco' ? 'core' : ''"
        @click="Tipoinfos('ListaPreco')"
      >
        Lista de preço
      </div>
    </div>
    <div class="information_prod">
      <div class="informacao">
        <div v-if="props.produto.tipo == 'precos'">
          <div class="Titulo"><strong>Preços</strong></div>
          <div class="FormPreco">
            <div class="informacoa_prod">
              <div>Preço de custo</div>
              <div>Preço medio</div>
              <div>Preço de venda</div>
            </div>
            <div id="inputs_informacao">
              <div>
                <input
                  type="text"
                  ref="inputRef"
                  :disabled="product.estado === 1"
                  v-model="product.preçocust"
                  @keyup="$emit('Precos', 'preçocust', product.preçocust)"
                  class="input_info"
                  placeholder="Preço de custo exemplo"
                />
              </div>
              <div>
                <input
                  type="text"
                  ref="inputRef"
                  :disabled="product.estado === 1"
                  v-model="product.preco_medio"
                  @keyup="$emit('Precos', 'preco_medio', product.preco_medio)"
                  class="input_info"
                  placeholder="Preço medio exemplo "
                />
              </div>
              <div>
                <input
                  type="text"
                  ref="inputRef"
                  :disabled="product.estado === 1"
                  v-model="product.preçovenda"
                  @keyup="$emit('Precos', 'preçovenda', product.preçovenda)"
                  class="input_info"
                  placeholder="Preço de venda exemplo"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="fornecedore" v-if="props.produto.tipo == 'fornecedore'">
          <div class="addFornecedor">
            <button @click="fornecedores.state = !fornecedores.state" type="button" class="dropdown-toggle">
                Adicionar fornecedor
            </button>
            <div v-if="fornecedores.state" class="list-items">

                <input type="text" autofocus @keyup="search" class="form-control" placeholder="pesquisa...">

                <div v-for="item in fornecedores.data" :key="item.id" @click="AdicionarFornecedor(item.id)">
                    {{item.nome}}
                </div>
            </div>
          </div>
        </div>
        <div
          v-if="props.produto.tipo == 'ListaPreco'"
          class="lista_de_preço_produto"
        >
          <div class="Titulo">
            <span><strong>Lista de preço</strong></span>
          </div>
          <div class="FormListPrice">
            <div class="preco_sem_disconto_list_price">
              <label><strong>Preço</strong></label>
              {{ FormetDineiro.format(product.preçovenda) }}
            </div>
            <div class="QuantidadeParaDesconto">
              <label>Quantadade</label>
              <input
                :disabled="product.estado === 1"
                v-model="ListPrice.quantidade"
              />
            </div>
            <div class="Desconto">
              <label>Preço de desconto</label>
              <input
                :disabled="product.estado === 1"
                type="text"
                v-model="ListPrice.preco"
              />
            </div>
          </div>
          <div @click="guadarListPrice" class="guadarListPrice">Guardar</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import { useStore } from "vuex";
import Progress from "@/components/confirmation/progress.vue";
import { useCurrencyInput } from "vue-currency-input";

const { inputRef } = useCurrencyInput({ currency: "AOA" });
const store = useStore();
const product = ref(props.produto.produtos);
const fornecedores = ref({
    state: false,
    data: [],
    store: []
})

const FormetDineiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});



const props = defineProps({
  produto: Object,
});

const ListPrice = ref({
  preco: FormetDineiro.format(0),
  quantidade: null,
  id: props.produto.produtos.id,
});

onMounted(async () => {
  await axios
    .get("/enformacoes_prod?IdProd=" + props.produto.produtos.id)
    .then((Response) => {
        fornecedores.value.data = Response.data.fornecedores
        fornecedores.value.store = Response.data.fornecedores
      if (Response.data.ListPrice) {
        ListPrice.value.preco = Response.data.ListPrice.preco_de_desconto;
        ListPrice.value.quantidade = Response.data.ListPrice.quantidade;
      }
    })
    .catch((err) => {
      console.log(err);
    });
});
const guadarListPrice = () => {
  store.state.StateProgress = true;
  axios
    .post("/inserir_informacao_prod", ListPrice.value)
    .then((Response) => {
      store.state.StateProgress = false;
    })
    .catch((Erro) => {
      console.log(Erro);
    });
};

const Tipoinfos = (event) => {
  props.produto.tipo = event;
};

function search (event) {
    const filter = fornecedores.value.store.filter((item) => {
       return String(item.nome).toLowerCase().includes(event.target.value.toLowerCase())
       || String(item.empresa).toLowerCase().includes(event.target.value.toLowerCase())
       || String(item.nif).includes(event.target.value)
    })
    fornecedores.value.data = filter;
}

function AdicionarFornecedor(fornecedor) {
    axios.post(`AddProdutoFornecedor/${props.produto.produtos.id}/${fornecedor}`,).then((response)=>{
        console.log(response.data);
    }).catch((err)=>{
        console.log(err);
    })
}
</script>

<style scoped lang="scss">
@import "../../../assets/produtos/css/informacao.scss";
</style>

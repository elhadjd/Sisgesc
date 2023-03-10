<template>
  <Progress v-if="ShowPrecess" />
  <div class="w-100 DivOrdensVendas">
    <div class="h-100" v-if="ModalProduct.estado">
      <Produto :produto="ModalProduct.produto" @descartar="OnMounted" />
    </div>
    <div v-else class="h-100">
      <div class="d-flex w-100 OrdenCima">
        <div class="OrdenCimaEsquerda w-50 h-100">
          <h4 class="text-secondary ms-4">Avaliação de stock</h4>
          <div class="geral w-75">
            <div class="d-flex w-100 titulos">
              <div>Total de custo</div>
              <div>Total de Venda</div>
              <div>Total de Lucro</div>
            </div>

            <div class="d-flex valores">
              <div>{{ FormatDinheiro.format(valores.totalCusto_geral) }}</div>
              <div>{{ FormatDinheiro.format(valores.totalVenda_geral) }}</div>
              <div>{{ FormatDinheiro.format(valores.totalLucro_geral) }}</div>
            </div>
          </div>
        </div>
        <div class="OrdenCimaDireita w-50">
          <span class="p-input-icon-right w-100">
            <i class="pi pi-search" />
            <input
              type="text"
              v-model="inputSearch"
              @keyup="SearchProd"
              class="SearchProduct"
              placeholder="Pesquisar... "
            />
          </span>
          <div class="Prints">
            <section class="agrupar">
              <div>
                <span class="dropdown-toggle">Agrupar</span>
                <div class="listGroup">
                  <span>Fornecedore</span>
                  <span>Departamento</span>
                  <span>Categoria</span>
                  <span>Tipo de artigo</span>
                </div>
                <div class="listGroups">
                  <span>ola</span>
                </div>
              </div>
            </section>
            <button @click="exportToPDF" class="print">
              <i class="fa fa-file"></i>
              Imprimir
            </button>
          </div>
        </div>
      </div>
      <div id="OrdenCima" class="FormBaixo">
        <div>
          <div
            class="titleOrdens text-end d-flex text-se components: { produto },condary"
          >
            <div class="w-25 text-start">Nome do artigo</div>
            <div>Quantidade</div>
            <div>Preço de custo</div>
            <div>Preço de venda</div>
            <div>Total de custo</div>
            <div>Total de venda</div>
            <div>Lucro unitario</div>
            <div>Total de lucro</div>
          </div>
          <div class="ListaOrden">
            <div
              @click="MostrarProduct(lista)"
              v-for="lista in lista"
              :key="lista.id"
              :class="
                lista.qtd == 0
                  ? 'bg-warning text-white'
                  : Number(lista.preçovenda) < Number(lista.preçocust)
                  ? 'bg-danger text-white'
                  : ''
              "
              class="d-flex text-end border-bottom ListaOrdens"
            >
              <div class="w-25 text-start item ProdName">{{ lista.nome }}</div>
              <div class="item">{{ lista.qtd }}</div>
              <div class="item">
                {{ FormatDinheiro.format(lista.preçocust) }}
              </div>
              <div class="item">
                {{ FormatDinheiro.format(lista.preçovenda) }}
              </div>
              <div class="item">
                {{ FormatDinheiro.format(lista.totalCusto) }}
              </div>
              <div class="item">
                {{ FormatDinheiro.format(lista.TotalVenda) }}
              </div>
              <div class="item">
                {{ FormatDinheiro.format(lista.MarginUnitario) }}
              </div>
              <div class="Lucro item">
                {{ FormatDinheiro.format(lista.TotalLucro) }}
              </div>
            </div>
          </div>
        </div>
        <div class="footer">
          <div>
            <span>Total custo</span>
            <strong>{{
              FormatDinheiro.format(valores.totalCusto_geral)
            }}</strong>
          </div>
          <div>
            <span>Total de Venda</span>
            <strong>{{
              FormatDinheiro.format(valores.totalVenda_geral)
            }}</strong>
          </div>
          <div>
            <span>Total de lucro</span>
            <strong>{{
              FormatDinheiro.format(valores.totalLucro_geral)
            }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import html2pdf from "html2pdf.js";
import { onMounted, reactive, ref } from "@vue/runtime-core";
import axios from "axios";
import { useStore } from "vuex";
import Produto from "../produtos/produto.vue";
import Progress from "../confirmation/progress.vue";
const inputSearch = ref("");
const store = useStore();
const lista = ref();
const valores = ref([]);
const StoreProduct = ref([]);
const ShowPrecess = ref(false);

const ModalProduct = reactive({
  estado: false,
  produto: Object,
});

const MostrarProduct = (event) => {
  ModalProduct.estado = true;
  ModalProduct.produto = event;
};

const confirmar = (id) => {
  axios
    .post(`/confirmProd/${id}`)
    .then((response) => {
      OnMounted();
    })
    .catch((err) => {
      console.log(err);
    });
};

const FormatDinheiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});
const OnMounted = onMounted(() => {
  ShowPrecess.value = true;
  axios
    .get("/ArtigosAvaliation")
    .then((Response) => {
      lista.value = Response.data.Lista;
      valores.value = Response.data.venda_geral;
      StoreProduct.value = Response.data.Lista;
      ShowPrecess.value = false;
      if (inputSearch.value != "") SearchProd(inputSearch.value);
    })
    .catch((err) => {
      console.log(err);
    })
    .finally(() => {
      ModalProduct.estado = false;
    });
});

const SearchProd = (event) => {
  if (inputSearch.value === "false") {
    const Filtrar = StoreProduct.value.filter((object) => {
      return object.qtd <= 0;
    });
    return (lista.value = Filtrar);
  }
  const Filtrar = StoreProduct.value.filter((object) => {
    return (
      String(object.nome)
        .toLowerCase()
        .includes(inputSearch.value.toLowerCase()) ||
      String(object.preçovenda)
        .toLowerCase()
        .includes(inputSearch.value.toLowerCase())
    );
  });
  return SumProducts(Filtrar);
};

const SumProducts = (products) => {
  valores.value.totalCusto_geral = 0;
  valores.value.totalVenda_geral = 0;
  valores.value.totalLucro_geral = 0;
  products.forEach((product) => {
    valores.value.totalCusto_geral += product.preçocust * product.qtd;
    valores.value.totalVenda_geral += product.preçovenda * product.qtd;
    valores.value.totalLucro_geral =
      valores.value.totalVenda_geral - valores.value.totalCusto_geral;
  });
  lista.value = products;
};

const exportToPDF = () => {
  const item = document.getElementById("OrdenCima");
  var opt = {
    margin: 0,
    filename: "List_products",
    html2canvas: { scale: 2 },
  };

  html2pdf().set(opt).from(item).save();
};
</script>

<style scoped lang="scss">
@import "../../../assets/PontoVenda/css/avaliation.css";
</style>

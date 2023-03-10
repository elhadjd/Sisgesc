<template>
  <OrderSingleUser
    @closeModal="modalSingle.state = false"
    v-if="modalSingle.state"
    :idCaixa="modalSingle.id"
  />
  <div class="principal">
    <div class="Header">
      <div class="Header-left">
        <div><h2>Ponto de venda</h2></div>
        <div v-if="user.nivel == 'Administrador'">
          <button @click="$emit('CriarCaixa')">Criar caixa</button>
        </div>
      </div>
      <div class="Header-right"></div>
    </div>
    <div class="Container">
      <div v-for="item in caixas" :key="item.id" class="cardCash">
        <div class="cashHeader">
          <div>
            <div>{{ item.name }}</div>
          </div>
          <div class="bars">
            <div>
              <i @click="OptionCaixas(item.id)" class="fa fa-bars"></i>
            </div>
            <div v-if="OptionCaixa == item.id" class="options">
              <div
                @click="
                  user.nivel == 'Administrador'
                    ? showOrders(item.id)
                    : $emit('message', 'info', 'Asuario sem acesso')
                "
              >
                Ordens
              </div>
              <div @click="$emit('sessao', item.id)">Sessões</div>
              <div
                @click="
                  user.nivel == 'Administrador'
                    ? $emit('definicaoCaixa', item)
                    : $emit('message', 'info', 'Asuario sem acesso')
                "
              >
                Definições
              </div>
            </div>
          </div>
        </div>
        <div class="cash-container">
          <div v-if="item.state != 'Aberto'">
            <button @click="$emit('caixa', 'AbrirCaixa', item.id), AbrirCaixa">
              Abrir
            </button>
          </div>

          <div v-if="item.state == 'Aberto'">
            <button @click="submit(item.id)" type="submit">Continuar</button>
          </div>
          <div v-if="item.state == 'Aberto'">
            <button
              @click="
                user.nivel == 'Administrador'
                  ? showCash(caixa.id)
                  : $emit('message', 'info', 'Asuario sem acesso')
              "
              class="showCash"
              type="button"
            >
              Ver
              <FontAwesomeIcon icon="fa fa-eye" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { computed, onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import OrderSingleUser from "./orderSingleUser.vue";
import { useStore } from "vuex";
import { useForm } from "@inertiajs/vue3";
const store = useStore();

const props = defineProps({
  dados: Object,
});

const modalSingle = ref({
  state: false,
  id: "",
});

const user = computed(()=> store.state.user);

const emits = defineEmits(["message"]);

const form = useForm({
  id_da_caixa: null,
});

const OptionCaixa = ref();

const caixas = ref(props.dados.caixas);

onMounted(() => {
getCaixas();
});

const submit = (id) => {
  axios
    .get(`/getCashConfig/${id}`)
    .then((response) => {
      store.commit("StoreConfigCash", response.data);
      form.id_da_caixa = id;
      form.post("/HeaderPos", {
        onSuccess: (Response) => {
          emits("message", "info", Response.props.message);
        },
      });
    })
    .catch((err) => {
      console.log(err);
    });
};

const OptionCaixas = (event) => {
  if (OptionCaixa.value == event) {
    OptionCaixa.value = null;
  } else {
    OptionCaixa.value = event;
  }
};

const showOrders = (id) => {
  modalSingle.value.id = id;
  modalSingle.value.state = true;
};

const showCash = (Cash) => {
  axios
    .get(`caixa/show/${Cash}`)
    .then((response) => {
      emits("AbrirCaixa", response.data);
    })
    .catch((err) => {
      console.log(err);
    });
};
const getCaixas = () => {
    axios.get('caixa/buscar')
  .then((Response) => {
      caixas.value = Response.data
  }).catch((err) => {
      console.log(err);
  });
}
</script>

<style scoped lang="scss">
@import "../../../assets/PontoVenda/css/MenuPonto.scss";
</style>

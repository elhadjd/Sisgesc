<template>
<Progress v-if="$store.state.StateProgress"/>
  <div class="Muvemento w-100">
    <div class="w-100 h-100">
        <div class="d-flex w-100 OrdenCima">
            <div class="OrdenCimaEsquerda w-50">
                <h3 @click="FecharMuv" class="TipoMuve ms-4">Analise de {{$store.state.EstadoMuvemento.tipo == 'Muvementos'? 'Muvementos':$store.state.EstadoMuvemento.tipo == 'Entrada'? 'Entrada': 'Saida' }} de {{product.nome}} </h3>
            </div>
            <div class="w-50">
                <span class="p-input-icon-right w-100">
                    <i class="pi pi-search" />
                    <InputText @keyup="Pesquisar" v-model="data" placeholder="Pesquisar... Exemplo 2022-09-21" class="w-100 p-2"/>
                </span>
                <span class="w-50" v-show="$store.state.EstadoMuvemento.tipo == 'Muvementos'">
                    <select class="w-50" v-model="Select">
                        <option >{{Select}}</option>
                        <option v-if="Select != 'Muvementos'">Muvementos</option>
                        <option v-if="Select != 'Entrada'">Entrada</option>
                        <option v-if="Select != 'Saida'">Saida</option>
                        <option v-if="Select != 'Entrada&Saida'">Entrada&Saida</option>
                    </select>
                </span>
            </div>
        </div>
        <div class="ListaMuvemento">
            <div class="d-flex titleOrden border-bottom w-100">
                <strong class="d-flex w-100">
                    <div class="w-20">Artigos</div>
                    <div>Ref Da Orden</div>
                    <div>Funcionario</div>
                    <div>Tipo de operação</div>
                    <div>Motivo</div>
                    <div>Data</div>
                    <div class="text-end px-3">Quantidade</div>
                </strong>
            </div>
            <div class="overflow-auto ListaOrden">
                <div v-if="Select != 'Muvementos'">
                    <div v-for="lista in listamuv" :key="lista.id" :class="lista.id_do_artigo != product.id ? 'd-none':''" class="Esconder border-bottom">
                        <div class="d-flex ListaDeMuvementos">
                            {{lista.id}}
                            <div class="w-25">{{product.nome}} </div>
                            <div>{{'Orden '+lista.id_da_orden}}</div>
                            <div>{{lista.apelido}}</div>
                            <div>{{lista.tipo_de_operacao}}</div>
                            <div>{{lista.motive}}</div>
                            <div>{{lista.data}}</div>
                            <div class="text-end px-3">
                                <i :class="lista.tipo_de_operacao == 'Saida' ? 'fa fa-arrow-down Saidas':lista.tipo_de_operacao == 'Entrada' ? 'fa fa-arrow-up text-success Entradas': 'fa fa-cart-arrow-down text-warning'"></i>
                                {{lista.quantidade}}
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else v-for="data in datas" :key="data.id" class="MostrarLista">
                    <div @click="BuscarMuvemento(data)" class="ListData">
                        <span>{{'dia '+data.dia+' de '+data.mes+' de '+data.ano}}</span>
                        <span>{{'Entrada: '+data.entrada}}</span>
                        <span>{{'Saida: '+data.saida}}</span>
                    </div>
                    <div v-if="LinhaMostrar == data.id && estado == true" class="h-25 LinhaMostrar">
                        <div v-for="lista in listamuv" :key="lista.id" class="Esconder border-bottom">
                            <div class="d-flex ListaDeMuvementos">
                                <div class="w-25">{{product.nome}} </div>
                                <div>{{'Orden '+lista.id_da_orden}}</div>
                                <div>{{lista.apelido}}</div>
                                <div>{{lista.tipo_de_operacao}}</div>
                                <div>{{lista.motive}}</div>
                                <div>{{lista.data}}</div>
                                <div class="text-end px-3">
                                    <i :class="lista.tipo_de_operacao == 'Saida' ? 'fa fa-arrow-down Saidas':lista.tipo_de_operacao == 'Entrada' ? 'fa fa-arrow-up text-success Entradas': 'fa fa-cart-arrow-down text-warning'"></i>
                                    {{lista.quantidade}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import Progress from '@/components/confirmation/progress.vue'
import InputText from 'primevue/inputtext'
import axios from 'axios';
import { onMounted , ref, watch} from '@vue/runtime-core';
import { useStore } from 'vuex';
const LinhaMostrar = ref()
const datas = ref();
const store = useStore()
const data = ref();
const listamuv = ref()
const estado = ref(false);
const Select = ref('Muvementos')
const props = defineProps({product: Object})

const product = ref([])
onMounted(() => {
    store.state.StateProgress = true
    axios.post(`/Muvementos/${props.product.id}`)
    .then((Response) => {
        datas.value = Response.data;
        product.value = props.product
        store.state.StateProgress = false
    }).catch((err) => {
        console.log(err);
    });
})

const FecharMuv =(()=>{
    store.state.EstadoMuvemento.estado = false
})

watch(Select, (newX) => {
    if (newX != 'Muvementos') {
        store.state.StateProgress = true
        axios.post(`/Muvementos`,{
            escolha: newX,
            produto: product.value.id
        })
        .then((Response) => {
            listamuv.value = Response.data
            store.state.StateProgress = false
        }).catch((err) => {
            console.log(err);
        });
    }
})

const Pesquisar = (()=>{
    axios.post(`/Muvementos/${props.product.id}`,{data:data.value})
    .then((Response) => {
        datas.value = Response.data;
    }).catch((err) => {
        console.log(err);
    });
})

const BuscarMuvemento = ((event) =>{
    if (LinhaMostrar.value != event.id) {
        estado.value = false
    }
    estado.value = !estado.value,
    listamuv.value = null
    if (estado.value == true) {
        store.state.StateProgress = true
        LinhaMostrar.value = event.id
        axios.post('/Muvementos',{
            data: event,
            tipo: store.state.EstadoMuvemento.tipo,
            produto: product.value.id
        })
        .then((Response) => {
            listamuv.value = Response.data
            store.state.StateProgress = false
        }).catch((err) => {
            console.log(err);
            store.state.StateProgress = false
        });
    }
})
</script>

<style scoped lang="scss">

@import '../../../assets/produtos/css/muvementos';

</style>

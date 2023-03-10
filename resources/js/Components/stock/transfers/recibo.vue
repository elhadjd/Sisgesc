<template>
    <div class="Principal">
        <div class="Modal">
            <div class="Header">
                <h2>Comprovativo da Transferencia</h2>
                <span>
                    <button class="Imprimir" @click="exportToPDF">Imprimir</button>
                    <button class="Fechar" @click="$emit('closeModal')">X</button>
                </span>

            </div>

            <div id="Container" class="Container">
                <div class="ContainerOne">
                    <div class="LogoEmpresa">
                        <img :src="`/login/image/${empresa.imagem}`">
                    </div>
                    <div class="NomeEmpresa">
                        <span>{{empresa.nome_de_empresa}}</span>
                        <div>
                            {{FormatDate(Encomenda.order.created_at)}}
                        </div>
                    </div>
                </div>
                <div class="ContainerTwo">
                    <div class="InfoClienteEmpresa">
                        <div class="InfoEmpresa">
                            <div class="Titulo">
                                <span class="span">ENDEREÇO</span>
                                <span class="span">NIF</span>
                                <span class="span">TELEFONE</span>
                            </div>
                            <div class="Content">
                                <span class="span">
                                    {{empresa.cidade == "" ? 'Sem endereço' : empresa.cidade}}
                                </span>
                                <span class="span">{{empresa.nif_empresa}}</span>
                                <span class="span">{{empresa.phone}}</span>
                            </div>
                        </div>
                        <div class="">
                            <div class="NomeCliente">
                                {{Encomenda.order.NomeArmagen}}
                            </div>
                            <div class="InfoCliente">

                            </div>
                        </div>
                    </div>
                    <div class="ContainerFatura">
                        <div class="HeaderInvoice">
                            <span class="title">Artigo</span>
                            <span class="title">Quantidade</span>
                            <span class="title">Preço</span>
                            <span class="title">Gastos</span>
                            <span class="title">Total</span>
                        </div>
                        <div class="ContainerInvoice">
                            <div v-for="item in Encomenda.list" class="Items" :key="item.id">
                                <div class="item">{{item.nome}}</div>
                                <div class="item">{{item.quantity+'Un(s)'}}</div>
                                <div class="item">{{format.format(item.final_price)}}</div>
                                <div class="item">{{format.format(item.spend)}}</div>
                                <div class="item">{{format.format(item.total)}}</div>
                            </div>
                            <div class="Totals">
                                <div class="TotalsContainer">
                                    <div class="TotalMercadoria">
                                        <div class="totalMount">
                                            <strong>TOTAL MERCADORIA</strong>
                                            <strong>GASTOS</strong>
                                        </div>
                                        <div class="totalMount">
                                            <span>{{format.format(TotalMercadoria())}}</span>
                                            <span>{{format.format(props.encomenda.total_gastos)}}</span>
                                        </div>
                                    </div>
                                    <div class="TotalMercadoria">
                                        <div>
                                            <strong>TOTAL DO DOCUMENTO</strong>
                                        </div>
                                        <div>
                                            <span>{{format.format(props.encomenda.total)}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="TankYour">
                        <strong>{{Encomenda.order.estado != 'Confirmado' ? 'ESTE DOCUMENTO E A PENAS UMA PERFORMA' : ''}}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import html2pdf from "html2pdf.js";
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import { useStore } from "vuex";
import moment from 'moment'

const props = defineProps({
    encomenda: Object
})


const TotalMercadoria = (()=>{
    return Number(props.encomenda.total)
- Number(props.encomenda.total_gastos)
})

const Encomenda = ref({
    list: [],
    order: []
})
const store = useStore()

const Pagamentos = ref([])

const TotalDivida = ref([])

const format = Intl.NumberFormat('PT-AO',{
    style: 'currency',
    currency: 'AOA'
})

 const FormatDate = ((data) =>{
    return moment(data).format('DD-MM-YYYY H:mm:ss')
 })

const empresa = ref(store.state.Empresa);

onMounted(()=>{
    Select();
})

const Select = (()=>{
    axios.get(`/selectOrder/${props.encomenda.id}`)
    .then((response) => {
        console.log();
        Encomenda.value = response.data
    }).catch((err) => {
        console.log(err);
    });
})

const exportToPDF = (()=> {
  const item = document.getElementById("Container");
  var opt = {
    margin: 0,
    filename: Encomenda.value.order.id+'_'+Encomenda.value.order.NomeArmagen,
    html2canvas: { scale: 2},
  };

  html2pdf().set(opt).from(item).save();
})
</script>

<style scoped>

@import url('../../../../assets/Compra/css/fatura.css');
</style>

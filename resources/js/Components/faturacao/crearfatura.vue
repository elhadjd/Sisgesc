<template>
    <div class="Principal">
        <Fatura v-if="StateInvoice" @FecharModal="StateInvoice = false" :encomenda="Invoice"/>
        <Pagamento @message="message" v-if="StatePayment" @encomenda="UpdateInvoice" @fechar="StatePayment = false" :FormularioPagamentoCompra="FormularioPagamentoCompra"/>
        <div class="Header">
            <h1>Fatura</h1>
        </div>
        <div class="Container">
            <div class="Botoes">
                <button @click="ConfirmOrder" v-if="Invoice.estado.trim() == 'Cotação'" class="botoes">Confirmar</button>
                <button @click="$emit('CancelarFatura'),CancelarFatura" v-if="Invoice.estado.trim() == 'Cotação'" class="botoes">Fechar</button>
                <button @click="PayInvoice" v-if="Invoice.estado.trim() == 'Publicado'" class="botoes">Adicionar pagamento</button>
                <button @click="PrintInvoice" v-if="Invoice.estado.trim() != 'Cotação'" class="botoes">Imprimir fatura</button>
                <button @click="AnnularInvoice(Invoice.id)" v-if="Invoice.estado.trim() == 'Publicado'" class="discartar">Annular fatura</button>
            </div>
            <div class="Formulario">
                <div class="Form">
                    <div class="HeaderFatura">
                        <div class="NumeroOrdem">
                            <strong>Orden /{{Invoice.id}} </strong>
                        </div>
                        <div class="Informacao">
                            <div>
                                <div class="cliente">
                                    <div>
                                        <input type="text" placeholder="Selecionar Cliente"
                                        @click="SelectCliente" @keyup="PesquisarCliente"
                                        :disabled="Invoice.estado.trim() != 'Cotação'"
                                        v-model="Invoice.apelido"/>
                                        <!-- <i class="fa fa-plus"></i> -->
                                    </div>
                                    <div v-if="MostrarClientes" class="shadow ListCliente">
                                        <div class="clienteRows" @click="AddCliente(cliente)" v-for="cliente, index in Clients.list" :key="index">
                                            <div>
                                                {{index}}
                                            </div>
                                            <div>
                                                {{cliente.apelido}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="telefone">
                                    <input type="text" disabled :value="Invoice.telefone != '' ? Invoice.telefone : Invoice.whatssap" class="form-control" placeholder="Telefone"/>
                                </div>

                            </div>

                            <div>
                                <Termo :invoice="Invoice"/>
                            </div>
                        </div>
                    </div>
                    <div class="FooterFatura">
                        <FaturacaoPedido :Invoice="Invoice" @message="message"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Dropdown from "primevue/dropdown";
import { ref, onMounted,reactive} from "vue";
import { mapState,useStore } from "vuex"
import FaturacaoPedido from "./FaturacaoPedido.vue";
import Pagamento from '../pagamentos/index.vue'
import Termo from './termo.vue';
import Fatura from './fatura/index.vue';
import useEventsBus from "@/Eventbus";
import axios from "axios";
const props = defineProps({
    Invoice: Object
})

const {emit} = useEventsBus()

const emits = defineEmits(['message'])

const Invoice = ref(props.Invoice)

const selectedCountry = ref();

const loading = ref(true);

const Clients = ref({
    list: [],
    store: []
});

const StatePayment = ref(false)

const StateInvoice = ref(false)

const faturas = ref({
    fatura: null,
    pagamentos: null,
    resto: null
});

const FormularioPagamentoCompra = reactive({
    encomenda: Invoice.value,
    valor:Invoice.value.TotalFatura,
    metodo:[],
    data:null,
    fornecedor:Invoice.value['apelido'],
    caminho: `AddPaymentFatura/${Invoice.value.id}`,
    CalcularTotalAPagar: `CalcularTotalAPagarFaturacao/${Invoice.value.id}`
});

const UpdateInvoice = ((invoice) =>{
    Invoice.value = invoice
})

const AnnularInvoice = ((idInvoice)=>{
    axios.post(`/AnnularInvoice/${idInvoice}`)
    .then((response) => {
        Invoice.value = response.data.Invoice
    }).catch((err) => {
        console.log(err);
    });
})

const MostrarClientes = ref(false)

const store = useStore();
onMounted(()=> {
    store.state.PodeEditar = null
    axios.post("/cliente",{faturacao: 'lista'}).then((Response) => {
        Clients.value.list = Response.data;
        Clients.value.store = Response.data;
    });
})

const ConfirmOrder = ()=>{
    axios.post(`/ConfirmOrder/${Invoice.value.id}`)
    .then((Response)=>{
        if (!Response.data.menssagen) {
            Invoice.value = Response.data.Invoice
        } else {
            return message(Response.data.menssagen,Response.data.tipo)
        }
    })
}

const message = ((message,tipo)=>{
    emits('message',message,tipo)
})

const CancelarFatura = () => {
    store.state.form_faturacao = 'Ordens';
    store.state.OnLoad = false;
    store.state.prodFaturacao = [];
}

const PesquisarCliente = ((event)=>{
    const filter = Clients.value.store.filter((filte)=>{
        return String(filte.apelido).toLowerCase().includes(event.target.value.toLowerCase())
    })
    Clients.value.list = filter
})

const SelectCliente = (()=>{
    MostrarClientes.value = true
})

const AddCliente = ((event)=>{
    Invoice.value.IdCliente = event.id
    Invoice.value.apelido = event.apelido
    emit('Invoice',Invoice.value)
    MostrarClientes.value = false
});

const PrintInvoice = (()=>{
    StateInvoice.value = true
})

const PayInvoice = (()=>{
    StatePayment.value = true
})

const PodeEditar = ref(store.state.PodeEditar)

const FaturaFaturacao = ref(store.state.FaturaFaturacao)

</script>

<style scoped lang="scss">
@import '../../../assets/faturacao/css/CriarFaturas.scss';
</style>

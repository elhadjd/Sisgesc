<template>
	<div class="Principal">
        <div class="Header">
            <div class="titulo">
            <h1>{{ $store.state.titulo }}</h1>
            <button class="BotaoCrear" @click="Abrir_form_faturacao">Criar Fatura</button>
            </div>

            <div class="filtros">
                <span class="p-input-icon-right w-100">
                    <i class="fa fa-search"></i>
                    <input @keyup="SearchInvoice" type="text" placeholder="Pesquisar...">
                </span>
            </div>
        </div>
        <div class="Footer">
            <div class="TitlsListas d-flex w-100 text-secondary">
                <div>Orden</div>
                <div>Cliente</div>
                <div>Data da Encomenda</div>
                <div>Data de Vencimento</div>
                <div class="text-right">Total sem desconto</div>
                <div class="text-right">Total da fatura</div>
                <div>Resto a pagar</div>
                <div>Estado da fatura</div>
            </div>
            <div class="FormLista">
                <div class="d-flex ListaFaturas" @click="onRowSelect(ListaFatura)" v-for="ListaFatura in Faturação.ListaFaturas" :key="ListaFatura.id">
                    <div>{{'FT'+ListaFatura.created_at.substring(3, ListaFatura.created_at.length - 11)+'/00'+ListaFatura.id}}</div>
                    <div>{{ListaFatura.apelido}} </div>
                    <div>{{ ListaFatura.DataEncomenda != null ? formateDate(ListaFatura.DataEncomenda) : formateDate(ListaFatura.created_at) }} </div>
                    <div>{{ListaFatura.DataVencimento != null ? formateDate(ListaFatura.DataVencimento) : 'Não definido'}} </div>
                    <div class="text-end">{{FrmatDinheiro.format(ListaFatura.TotalMercadoria)}} </div>
                    <div class="text-end">{{FrmatDinheiro.format(ListaFatura.TotalFatura)}}</div>
                    <div :class="ListaFatura.EstadoPagamento">
                        <span>
                            {{ListaFatura.RestoApagar >=1 ? FrmatDinheiro.format(ListaFatura.RestoApagar): FrmatDinheiro.format(0)}}
                        </span>
                    </div>
                    <div :class="ListaFatura.estado">
                        <span class="">
                            {{ListaFatura.estado}}
                        </span>
                    </div>
                </div>
            </div>
            <div class="RodaPe">
                <div class="Totals">
                    <div>
                        <label for="">Total de faturas</label>
                        <span>{{FrmatDinheiro.format(Faturação.TotalFaturas)}}</span>
                    </div>
                    <div>
                        <label for="">Resto a pagar</label>
                        <span>{{FrmatDinheiro.format(Faturação.RestoALiquidar)}}</span>
                    </div>
                </div>
            </div>
        </div>
	</div>
</template>

<script setup>
import { ref, onMounted,defineEmits, reactive } from 'vue';
import { mapState ,useStore } from "vuex"
import moment from 'moment'

const Faturação = ref({
    StoreInvoice: [],
    TotalFaturas: 0,
    RestoALiquidar: 0,
    ListaFaturas: []
})

const emits = defineEmits(['fatura','message'])

onMounted(() => {
    axios.get('/BuscarFaturas').then((Response) => {
        Faturação.value.StoreInvoice  = Response.data.ListaFaturas
        Faturação.value.ListaFaturas  = Response.data.ListaFaturas
        SumInvoice(Faturação.value.ListaFaturas)
    }).catch((err) => {
        console.log(err);
    });
})

const formateDate = ((data)=>{
    return moment(data).format('DD-MM-YYYY')
})


    let FrmatDinheiro = ref(new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA'}))
    const store = useStore();


    const onRowSelect = (event) => {
        emits('fatura',event);
    };

    const Abrir_form_faturacao = () => {
        axios.get('/NovaFatura').then((result) => {
            emits('fatura',result.data.Invoice);
        }).catch((err) => {
            console.log(err);
        });
    }


    const SearchInvoice = ((event)=>{
        const FilterSearch = Faturação.value.StoreInvoice.filter(object=>{
            return String(object.apelido).toLowerCase().includes(event.target.value.toLowerCase())
            || String(object.created_at).toLowerCase().includes(event.target.value.toLowerCase())
            || String(object.TotalFatura).includes(event.target.value)
            || String(object.estado).toLowerCase().includes(event.target.value.toLowerCase())
        })
        if (FilterSearch.length<=0) {
            emits('message','Nenhuma fatura foi encontrada','info')
        }else{
            Faturação.value.ListaFaturas = FilterSearch
            return SumInvoice(FilterSearch)
        }
    })

    const SumInvoice = ((Invoices)=>{
        Faturação.value.TotalFaturas = 0
        Faturação.value.RestoALiquidar = 0
        Invoices.forEach(Invoice => {
            Faturação.value.TotalFaturas += Number(Invoice.TotalFatura)
            Faturação.value.RestoALiquidar += Number(Invoice.RestoApagar)
        });
    })

</script>
<style scoped lang="scss">
@import '../../../assets/faturacao/css/ListaFaturas.scss';
</style>

<template>
  <div class="ArquivoPedidos w-100">
    <div class="h-100">
        <div class="w-100 FormListaCima">
            <h4 class="ListOrdenClick ms-4 text-secondary" @click="$emit('fechar')">Ordens</h4>
        </div>
        <div class="w-100 FormListaBaixo">
            <div class="d-flex bg-white botoes rounded-0">
                <button type="button" @click="DevolverFatura" class="annular">
                    Devolver Artigos
                </button>
            </div>
            <div class="FormListPedidos">
                <div class="FormListPedido" >
                    {{Encomenda.encomenda.estado}}
                </div>
                <div class="FormList">
                    <div class="mt-4 text-secondary">
                        <div class="d-flex">
                            <div class="font-weight-600 InformacoesEncomenda">
                                <div>Ref da Orden</div>
                                <div>Sessão</div>
                                <div>Caixa</div>
                                <div>Cliente</div>
                            </div>
                            <div class="border ms-5 mt-1"></div>
                            <div class="Infos ms-4">
                                <div>{{"Orden"+Encomenda.encomenda.id}}</div>
                                <div>{{"Sessão"+Encomenda.encomenda.id_session}} </div>
                                <div>{{Encomenda.caixa.nome}} </div>
                                <div>{{Encomenda.encomenda.cliente}} </div>
                            </div>
                        </div>
                    </div>
                    <div class="BorderBottom mt-3">
                        <div class="d-flex titloListPedidos text-secondary border-0 bg-light rounded-0">
                            <div class="w-25 boder-left">Artigos</div>
                            <div class="boder-left text-end">Quantidade</div>
                            <div class="boder-left text-end">Preço Unitario</div>
                            <div class="boder-left text-end">Disconto</div>
                            <div class="boder-left text-end">Emposto</div>
                            <div class="boder-left text-end">Total</div>
                        </div>
                        <div v-for="Lista in Encomenda.Listas" :key="Lista.id" class="d-flex ListPedidos text-secondary">
                            <div class="w-25">{{Lista.nome}}</div>
                            <div class="text-end">{{(Lista.quantidade+'Un(s)')}}</div>
                            <div class="text-end">{{FormatDinheiro.format(Lista.preco_venda)}}</div>
                            <div class="text-end">{{FormatDinheiro.format(Lista.total_desconto)}}</div>
                            <div class="text-end">{{}}</div>
                            <div class="text-end">{{FormatDinheiro.format(Lista.total)}}</div>
                        </div>

                    </div>
                    <div class="w-100 form-control-sm text-secondary">
                        <div class="d-flex Totaless w-25 ">
                            <div class="TitleTotaies w-50">
                                <div>Total : </div>
                                <div>Valor Pago :</div>
                                <div>Troco :</div>
                                <div>Margin :</div>
                            </div>
                            <div class="Totaies w-50">
                                <div>{{FormatDinheiro.format(Encomenda.encomenda.total)}}</div>
                                <div>{{FormatDinheiro.format(Encomenda.total_pago)}}</div>
                                <div>{{FormatDinheiro.format(Encomenda.pagamento.troco)}}</div>
                                <div>{{FormatDinheiro.format(Encomenda.margin)}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Toast />
</div>
</template>

<script setup>
import { computed, onMounted , reactive, ref, toRefs} from '@vue/runtime-core'
import Toast from "primevue/toast";
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    ListPedido: Object
})
const emits = defineEmits(['fechar'])


onMounted(async ()=>{
    await BuscarEncomenda();
})

const toast = useToast()
const Encomenda = reactive({
    Listas: null,
    encomenda: [],
    margin: null,
    total_pago:null,
    pagamento: [],
    caixa: []
});

const FormatDinheiro = new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA',})

const DevolverFatura = (()=>{
    if (Encomenda.encomenda.estado == 'Annulado') {
        toast.add({severity:'error', summary: 'Fatura', detail:'Esta fatura ja foi anulada ', life: 5000});
        return false
    } else {
        axios.post('/AnnularOrdenden',{
            IdEncomenda: props.ListPedido.id
        })
        .then((response) => {
            Encomenda.Listas = response.data.pedidos
            Encomenda.encomenda = response.data.encomenda
            Encomenda.margin = response.data.margin
            Encomenda.total_pago = response.data.totalPago
            Encomenda.pagamento = response.data.pagamento
            Encomenda.caixa = response.data.caixa
            toast.add({severity:'success', summary: 'Fatura', detail:'Fatura anulada com sucesso !', life: 5000});
        }).catch((err) => {
            console.log(err);
        });
    }
});

const BuscarEncomenda = (async()=>{
    await axios.post('/ListPedido',{
        IdEncomenda: props.ListPedido.id
    })
    .then((response) => {
        Encomenda.Listas = response.data.pedidos
        Encomenda.encomenda = response.data.encomenda
        Encomenda.margin = response.data.margin
        Encomenda.total_pago = response.data.totalPago
        Encomenda.pagamento = response.data.pagamento
        Encomenda.caixa = response.data.caixa
    }).catch((err) => {
        console.log(err);
    });
});

</script>

<style scoped>
@import '../../../assets/FiltrosPesquisas/css/encomenda.scss';
</style>

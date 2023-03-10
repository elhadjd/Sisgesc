<template>
<div class="Form_fatura">
    <div id="EmpresaEnfo">
        <h2 class="">{{Methos_Fatura.empresa.nome_de_empresa}}</h2>
        <div class="info-company" v-if="config.infoCompany === '1'">
            <div class="content">
                <span>TELEFONE : </span>
                <div class="numero">{{Methos_Fatura.empresa.phone}}</div>
            </div>
            <div class="content">
                <span>NIF DA EMPRESA : </span>
                <div class="numero">{{Methos_Fatura.empresa.nif_empresa}}</div>
            </div>
        </div>
    </div>

    <div class="Cliente">
        <strong class="title">Cliente</strong>
        <div class="InfoCliente">
            <div>
                <strong>Nome : </strong>
                <span id="NomeCliente">{{Methos_Fatura.encomenda.cliente}} </span>
            </div>
            <div>
                <strong>Telefone : </strong>
                <span>{{"+244"}} </span>
            </div>
        </div>
    </div>

    <div>
        <div class="Titls">
            <div class=""><strong>Quantidade</strong></div>
            <div class=""><strong>Preço</strong></div>
            <div class="totalTitl"><strong>Total</strong></div>
        </div>

        <div class="listaItens">
            <div class="item" v-for="fatura in ListItem" :key="fatura.nome">
                <div>{{fatura.nome}} </div>
                <div class="PrecoQuant">
                    <div class="QuantidadeFatura">{{fatura.quantidade+'Un(s)'}}</div>
                    <div class="PrecoFatura">{{FormetDineiro.format(fatura.preco_venda)}}</div>
                    <div class="TotalFatura">{{FormetDineiro.format(fatura.total)}}</div>
                </div>
            </div>
        </div>

        <div id="BaixoFatura">
            <div class=" InfoPag">

                <div class="TiposPag" >
                    <div><strong >Total</strong></div>
                    <div v-if="Methos_Fatura.encomenda.numerario> 0"><strong id="NumerarioTitl">Numerario</strong></div>
                    <div v-if="Methos_Fatura.encomenda.multicaixa> 0"><strong id="multicaixaTitl">Multicaixa</strong></div>
                    <div v-if="Methos_Fatura.encomenda.transferencia> 0 "><strong id="trasferenciaTitl">Transferencia</strong></div>
                    <div><strong>Troco</strong></div>
                    <div><strong>Operador</strong></div>
                </div>

                <div class="totais">
                    <div id="TotalFatura">{{FormetDineiro.format(Methos_Fatura.encomenda.total)}}</div>
                    <div id="Numerario" v-if="Methos_Fatura.encomenda.numerario> 0">{{FormetDineiro.format(Methos_Fatura.encomenda.numerario)}} </div>
                    <div id="multicaixa" v-if="Methos_Fatura.encomenda.multicaixa> 0">{{FormetDineiro.format(Methos_Fatura.encomenda.multicaixa)}}</div>
                    <div id="transferencia" v-if="Methos_Fatura.encomenda.transferencia> 0">{{FormetDineiro.format(Methos_Fatura.encomenda.transferencia)}}</div>
                    <div id="troco">{{FormetDineiro.format(Methos_Fatura.encomenda.troco)}}</div>
                    <div id="operador">{{Methos_Fatura.encomenda.apelido}}</div>
                </div>

            </div>
        </div>

        <div class="data_fatura">
            <div>{{Methos_Fatura.encomenda.created_at}}</div>
            <div>{{"Id :"+Methos_Fatura.encomenda.id}}</div>
        </div>
        <div class="Obrigado">OBRIGADO VOLTE SEMPRE !!!</div>
    </div>
</div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core"
import { useStore } from "vuex"
const props = defineProps({
    dadosFatura: Object
})
const store = useStore()
const config = ref(store.state.configCash)
const FormetDineiro = new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA',})
const ListItem = ref(props.dadosFatura.list)
const Methos_Fatura = ref(props.dadosFatura.info[0])

onMounted(async ()=>{
    window.print()
})

</script>

<style scoped lang="scss">
    @import '../../../assets/Pos/css/fatura.scss';
</style>

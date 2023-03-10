<template>
<div class="DivOrdensVendas">
    <div class="Container">
        <div class="OrdenCima">
            <div class="GastosCimaEsquerda">
                <h4>GASTOS</h4>
                <button @click="FormGasto = true" class="AdicionarGastos">Adicionar</button>
            </div>
            <div class="GastosCimaDireita">
                <span class="p-input-icon-right w-100">
                    <i class="pi pi-search" />
                    <input type="text" @keyup="Pesquisar" name="PesquuisarGasto" placeholder="Pesquisar por data , Exemplo(01-01-2001)" id="PesquuisarGasto" class="w-100 p-2 form-control">
                </span>
            </div>
        </div>
        <div class="list-content">
            <div class="titleGastos">
                <div>Id de muvemento</div>
                <div>Assunto</div>
                <div>Funcionario</div>
                <div>Data</div>
                <div class="TotalOrden">Total</div>
            </div>
            <div class="ListaOrden">
                <div>
                    <div @click="showGasto(listas)" v-for="listas , index in Resposta.lista" :key="index" class="ListaGastos">
                        <div><strong>{{index}}</strong></div>
                        <div>{{listas.assunto}}</div>
                        <div>{{listas.apelido}}</div>
                        <div>{{listas.dataPesquisa}}</div>
                        <div class="TotalOrden">{{FormatDinheiro.format(listas.valor)}}</div>
                    </div>
                </div>

                <div class="TotalGastos">
                    <span class="">total: </span>
                    <div>{{FormatDinheiro.format(Resposta.total)}}</div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="FormGasto" class="BlocoGasto">
        <div class="Modal">
            <div class="Header">
                <h3>NOVO GASTO</h3>
            </div>
            <div class="Content">
                <div class="w-100">
                    <textarea name="Assunto" id="Assunto" v-model="NovoGasto.assunto" placeholder="Assunto obligatorio" class="w-100" ></textarea>
                </div>
                <div class="d-flex mt-3">
                    <div class="w-50 text-secondary pt-1 d-flex">
                        <label for="">Responsavel :</label>
                        <h4 class="ms-3">{{Resposta.user.apelido}}</h4>
                    </div>
                    <div class="w-50">
                        <label for="ValorGasto">Valor</label>
                        <input type="number" class="form-control ValorGasto" v-model="NovoGasto.valor" placeholder="Digite valor a gastar" name="ValorGasto" id="ValorGasto">
                    </div>
                </div>
                <div class="d-flex BlocoNovoGastoBaixo">
                    <div @click="FormGasto = false" class="facharJanela mt-2">Fechar</div>
                    <button @click="EnviarGasto" class="ConfirmarGasto ms-2 mt-2">
                        Conformar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { onMounted, reactive, ref } from "@vue/runtime-core";
import axios from "axios";

    const Resposta = reactive({
        user:[],
        lista: null,
        storeGastos: [],
        total: null,
    })

    const NovoGasto = ref({
        id:null,
        responsavel: Resposta.user.id,
        assunto: null,
        valor: null,
    });

    const FormGasto = ref(false);

    const FormatDinheiro = new Intl.NumberFormat('pt-AO',{style: 'currency', currency: 'AOA'})

    const EnviarGasto = (()=>{
        NovoGasto.value.responsavel = Resposta.user.id
        axios.post('/AdicionarGasto',{
            Guardar: NovoGasto.value
        })
        .then((Response) => {
            Resposta.lista = Response.data.ListaGastos;
            Resposta.total = Response.data.total,
            Resposta.user = Response.data.user
            FormGasto.value = false
        }).catch((err) => {
            console.log(err);
        });
    })

    const Pesquisar = ((event)=>{
        const filter = Resposta.storeGastos.filter((gasto)=>{
            return String(gasto.dataPesquisa).includes(event.target.value)
        })
        Resposta.lista = filter
        return sumGastos()
    })

    onMounted(()=>{
        axios.get('/AdicionarGasto?lista=toda')
        .then((Response) => {
            Resposta.lista = Response.data.ListaGastos;
            Resposta.storeGastos = Response.data.ListaGastos
            Resposta.total = Response.data.total,
            Resposta.user = Response.data.user
        }).catch((err) => {
            console.log(err);
        });
    })

    const showGasto = ((abject)=>{
        NovoGasto.value = abject
        FormGasto.value = true
    })

    const sumGastos = (()=>{
        Resposta.total = 0
        Resposta.lista.forEach((lista)=>{
            Resposta.total += Number(lista.valor)
        })
    })
</script>

<style scoped lang="scss">
@import '../../../assets/PontoVenda/css/gastos';
</style>

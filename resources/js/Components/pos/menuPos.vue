<template>
<div class="ManuPosGeral">
    <Index v-if="!$store.state.Controlo.estado" @LogIn="LogIn" :Cash="props.Cash" @message="menssagens"/>
    <Toast/>
    <Transition>
        <EntradaSaida v-if="entradaSaida" @message="menssagens" @fechar="entradaSaida=false"/>
    </Transition>
    <div v-if="fatura" class="fatura">
        <Fatura :dadosFatura="dadosFatura"/>
    </div>

    <section id="headerPos">
        <div class="header-One">
            <li>
                <strong> SISGESC </strong>
            </li>
            <div class="EntradaSaida" @click="entradaSaida = true">
                <cash/>
                <span>Entrada & Saida & gastos</span>
            </div>
            <div  @click="ListePedidos = true" id="pos">
                <i class="fa fa-ticket" aria-hidden="true"></i>
                <strong>{{ Pedido.NumPedido }}</strong>
                <span>Pedido</span>
            </div>
        </div>
        <div class="header-two">
            <Pesquisar/>
            <div id="UsuarioConectado">
                <div>
                    <div>{{ $store.state.user.apelido }}</div>
                </div>
                <div class="SairPos">
                    <Link :href="route('SairPos')">Close <i class="fa fa-sign-out"></i></Link>
                </div>

            </div>
        </div>
    </section>
    <section class="MenuPos user-select-none">
        <div class="cartShop">
            <shopping :size="200"/>
        </div>
        <div class="Posesquerda">
            <div style="height: 100%">
                <div class="esquerdaCima">

                    <div class="Carrinho">
                        <TransitionGroup name="list" tag="ul">
                        <li
                        class="listaPedido"
                        v-for="Pedido , index in Pedido.lista"
                        :key="index"
                        @click="ClicarLinha(Pedido.id,'linha')"
                        >
                            <div class="w-50">{{ Pedido.nome }}</div>
                            <div class="mr-3 w-auto">{{ Pedido.quantidade+'Un(s)' }}</div>
                            <div>{{ FormatarDineiro.format(Pedido.preco_pedido) }}</div>
                            <div class="totalEncomeda">{{ FormatarDineiro.format(Pedido.total) }}</div>
                        </li>
                        </TransitionGroup>
                        <div v-if="Pedido.lista.length>0" class="totalEncomendase">
                            <div class="totalEncomenda"><strong class="mr-2">Total:</strong>{{FormatarDineiro.format(Pedido.total)}}</div>
                            <div class="posAtivo">{{Pos}}</div>
                        </div>
                    </div>

                </div>

                <div class="esquerdaBaixo">
                    <Eventos :session="Pedido.session" @FazerPagamento="FazerPagamento" @cliente="client"
                     @Alterar="Alterar" @tipo="tipo" @Remover="Remover"/>
                </div>
            </div>
        </div>
        <div class="Posdireita">
            <ListePedido @Fechar="ListePedidos = false" @AlterarPedido="AlterarPedido"
            @NovoPedido="NovoPedido" v-if="ListePedidos"/>
            <div v-else class="w-100 h-100">
                <div class="direitaCima">

                </div>
                <div v-if="!fatura" class="direitaBaixo">
                    <Produtos @AddProds="AddProds" @message="menssagens"/>
                </div>
            </div>
        </div>
            <Pagamento :method="method"
            v-if="Form_Pagamento==true"
            @message="menssagens"
            @fechar_pagamento="Form_Pagamento = false"
            @fatura="Imprimir" :dados="Pedido" />
    </section>
</div>
</template>
<script setup>
import Index from './LogIn/index.vue'
import cash from 'vue-material-design-icons/CashMultiple.vue'
import shopping from 'vue-material-design-icons/Shopping.vue'
import {mapMutations, mapState, useStore } from "vuex"
import Fatura from './fatura.vue';
import Pagamento from './pagamento.vue';
import Produtos from "./produtos.vue";
import ListePedido from './ListePedidos.vue'
import Toast from 'primevue/toast'
import { Link } from '@inertiajs/inertia-vue3';
import EntradaSaida from './SaidaEntrada.vue'
import Eventos from './eventos.vue'
import './inatividade';
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { useToast } from 'primevue/usetoast';
import Pesquisar from './pesquisar.vue'

const store = useStore()
const ListePedidos = ref(false)
const FormatarDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: "AOA"})
const toast = useToast()
const pos = ref('Pos')
const entradaSaida = ref(false)
const cliente = ref('')
const IdAlterar = ref()
const dadosFatura = ref()
const fatura = ref(false)
const numeros = ref('')
const TipoAlteration = ref('quantidade')
const linha = ref(null)
const Form_Pagamento = ref(false)
const config = ref(store.state.configCash)
const Pedido = ref({
    data: null,
    lista: [],
    total: 0,
    estado: 'Cota????o',
    methodos: [],
    troco: null,
    user: null,
    cliente: null,
    NumPedido: null,
    session: null
});

const Encomendas = ref([])

const method = ref()

const props = defineProps({
    Cash: Object
})

const IdEncomenda = ref()


const LogIn = ((event)=>{
    store.commit('CloseCash',event)
})

const Imprimir = ((event) =>{
    dadosFatura.value = event
    fatura.value = true
    Form_Pagamento.value = false
    setTimeout(() => {
        fatura.value = false
    }, 50);
    Pedido.value.data = data()
    Pedido.value.NumPedido = null
    Pedido.value.lista = []
    Pedido.value.total = null
    store.state.ClientePos = null
    return OnMounted()
})

const InputFocus = (()=>{
    store.state.PesquisarProduto = ''
})

const AlterarPedido = ((event)=>{
    const VerifarEstado = Encomendas.value[event];
    if (VerifarEstado.estado !='Pago') {
        localStorage.setItem('NumeroPedidos'+Pedido.value.session,event)
        ListePedidos.value = false
        InputFocus()
        getStore()
    } else {
        return menssagens('info','Esta encomenda n??o pode ser alterada ')
    }
})

const NovoPedido = (()=>{
    //A verificar se o carrinho interior tem alguns items
    if (Pedido.value.lista.length <=0) {
        return menssagens('info','Aten????o n??o e possivel crear duas pedidos vazios')
    } else {
        Pedido.value.NumPedido = Encomendas.value.length
        Pedido.value.lista = []
        store.state.ClientePos = null
        Pedido.value.total = 0
        Pedido.value.cliente = null
        Pedido.value.data = data()
        localStorage.setItem('NumeroPedidos'+Pedido.value.session,Pedido.value.NumPedido)
        Encomendas.value.push(Pedido.value)
        localStorage.setItem('Encomendas'+Pedido.value.session,JSON.stringify(Encomendas.value))
        ListePedidos.value = false
    }
})

const client = ((event)=>{
    Pedido.value.cliente = event
    Encomendas.value[Pedido.value.NumPedido] = Pedido.value;
    localStorage.setItem('Encomendas'+Pedido.value.session,JSON.stringify(Encomendas.value))
})

const tipo = ((event)=>{
    numeros.value = ''
    TipoAlteration.value = event
})

const OnMounted = onMounted(async ()=>{
    await axios.get("/menuPos").then((Response) => {
        store.state.user = Response.data.User;
        method.value = Response.data.method
        Pedido.value.user = store.state.user
    });
    await axios.get("/buscarEncomenda").then((Response) => {
        localStorage.setItem('session',Response.data.session)
        Pedido.value.session = Response.data.session
        getStore()
    });
    InputFocus()
})

const getStore = (()=>{

    if (JSON.parse(localStorage.getItem('Encomendas'+Pedido.value.session))) {
        Encomendas.value = JSON.parse(localStorage.getItem('Encomendas'+Pedido.value.session))
        Pedido.value.NumPedido = localStorage.getItem('NumeroPedidos'+Pedido.value.session)
        //A verificar se esta encomenda esta paga
        return VerificarCarrinho(Encomendas.value[Pedido.value.NumPedido])
    } else {
        Pedido.value.cliente = null
        Pedido.value.data = data()
        Pedido.value.NumPedido = 0
        Encomendas.value.push(Pedido.value)
        Pedido.value.NumPedido = Encomendas.value.length -1
        localStorage.setItem('NumeroPedidos'+Pedido.value.session,Pedido.value.NumPedido)
        localStorage.setItem('Encomendas'+Pedido.value.session,JSON.stringify(Encomendas.value))
    }
})

const data = (() =>{
    var objData = new Date(),
    ano = objData.getFullYear(),
    mes = objData.getMonth()+1,
    numDias = new Date(ano, mes, 0).getDate();
    var dia = String(objData.getDate()).padStart('0')
    return dia+'-'+mes+'-'+ano;
})

const SetStore = (()=>{
    Pedido.value.total = CalcularTotal()
    Encomendas.value[Pedido.value.NumPedido] = Pedido.value;
    localStorage.setItem('Encomendas'+Pedido.value.session,JSON.stringify(Encomendas.value))
    getStore()
})

const VerificarCarrinho = ((event)=>{
    if (event.estado !='Pago') {
        if (event.lista!=[]) {
            store.state.ClientePos = event.cliente
            Pedido.value.lista = event.lista
            CalcularTotal()
        }
    } else {
        const testar = Encomendas.value.find(o=>o.estado === 'Cota????o');
        if (testar) {
            localStorage.setItem('NumeroPedidos'+Pedido.value.session,testar.NumPedido)
            Pedido.value.NumPedido = testar.NumPedido
            Pedido.value.lista = Encomendas.value[testar.NumPedido].lista;
            store.state.ClientePos = Encomendas.value[testar.NumPedido].cliente
            CalcularTotal()
        } else {
            Pedido.value.NumPedido = Encomendas.value.length
            localStorage.setItem('NumeroPedidos'+Pedido.value.session,JSON.stringify(Pedido.value.NumPedido))
            Pedido.value.NumPedido = localStorage.getItem('NumeroPedidos'+Pedido.value.session)
            Pedido.value.cliente = store.state.ClientePos
            Encomendas.value.push(Pedido.value)
            localStorage.setItem('Encomendas'+Pedido.value.session,JSON.stringify(Encomendas.value))
            CalcularTotal()
        }
    }
})

const ClicarLinha = ((event,prod)=>{
    IdAlterar.value = event
    numeros.value = ''
    linha.value = prod
})

const menssagens = ((tipo,message)=>{
    toast.add({
        severity: tipo,
        summary:'Menssagem',
        detail: message,
        life: 5000
    })
})

const AddProds = ((produto)=>{

    const existProduct = Pedido.value.lista.find(object=>object.id === produto.id);
    if (existProduct) {
        // A verificar se este produto este produto tem desconto
        var lista_de_preco = JSON.parse(localStorage.getItem('Lista_de_preco'));
        var desconto = lista_de_preco.find(o => o.id_produto === existProduct.id)
        var preco = 0;
        if (desconto) {
            if (existProduct.quantidade + 1 >= desconto.quantidade) {
                preco = desconto.preco_de_desconto
            } else {
                preco = existProduct.pre??ovenda
            }
        } else {
            preco = existProduct.pre??ovenda
        }
        // A verificar se tem stock suficiente
        var quantidad = existProduct.quantidade + 1
        if (existProduct.qtd >= quantidad) {
            // a verificar se este produto tem disconto

            existProduct.quantidade += 1
            const total = preco * existProduct.quantidade
            existProduct.total = total
            existProduct.preco_pedido = preco

        } else {
            return menssagens('error','Este produto ja nao ten quantidade suficiente em stock')
        }

    } else {
        produto.total = produto.pre??ovenda
        produto.quantidade = 1
        produto.preco_pedido = produto.pre??ovenda
        Pedido.value.lista.push(produto)
        IdAlterar.value = produto.id
    }
    CalcularTotal();
    SetStore();
})

const Alterar = ((numero)=>{
    numeros.value = numeros.value + String(numero)
    const existProduct = Pedido.value.lista.find(o => o.id === IdAlterar.value)
    if (existProduct) {
        // A verificar se este produto este produto tem desconto
        var lista_de_preco = JSON.parse(localStorage.getItem('Lista_de_preco'));
        var desconto = lista_de_preco.find(o => o.id_produto === existProduct.id)
        var preco = 0
        if (desconto) {
            if (numeros.value >= desconto.quantidade) {
                preco = desconto.preco_de_desconto
            } else {
                preco = existProduct.pre??ovenda
            }
        } else {
            preco = existProduct.pre??ovenda
        }
        // A verificar o tipo de altera????o
        if (TipoAlteration.value === 'quantidade') {
            // A verificar se tem stock suficiente
            var quantidad = Number(numeros.value)
            if (existProduct.qtd >= quantidad) {

                existProduct.quantidade = quantidad
                const total = preco * existProduct.quantidade
                existProduct.total = total
                existProduct.preco_pedido = preco
                SetStore()
            } else {
                return menssagens('error','Este produto ja nao ten quantidade suficiente em stock')
            }
        } else {
            if (config.value.listPrice === "0") {
                return menssagens('info','Usuario sem aceso')
            } else {
                const total = numeros.value * existProduct.quantidade
                existProduct.total = total
                existProduct.preco_pedido = numeros.value
            }
        }
        SetStore();
        CalcularTotal();
    }
})



const Remover = (()=>{
    if (Pedido.value.lista != null) {
    let newList = Pedido.value.lista.filter(item => item.id !== IdAlterar.value);
    Pedido.value.lista = newList
    } else {
        Pedido.value.lista.pop();
    }
    CalcularTotal();
    SetStore()
    let ultimo = Pedido.value.lista[Pedido.value.lista.length - 1];

    if (ultimo) {
    IdAlterar.value = ultimo.id
    }
})

const CalcularTotal = () => {
    // aqui vai a l??gica do total
    Pedido.value.total = 0
    var ss = Pedido.value.lista.filter(novo => novo.total);
    ss.forEach(novo => {
        Pedido.value.total += Number(novo.total)
    });
    return Pedido.value.total
}


const FazerPagamento = (event) => {
    // A verificar se existe alguns total para pagar
    if (Pedido.value.lista.length <= 0) {
        Form_Pagamento.value = false
        return menssagens('info', 'N??o tem nehum item no carrinho por favor adicione')
    } else {
        IdEncomenda.value = Pedido.value.NumPedido
        Form_Pagamento.value = true
    }
}
</script>
<style lang="sass" scoped>
@import '../../../assets/Pos/css/MenuPos'
</style>

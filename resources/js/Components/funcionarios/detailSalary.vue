<template>
  <div class="principal">
    <div id="Container" class="Modal" >
        <div class="Header">
            <span>
                <img :src="`/produtos/image/${company.imagem}`" :alt="company.nome_de_empresa">
            </span>
            <span>
                {{company.nome_de_empresa}}
            </span>
            {{formatDate()}}
        </div>
        <div class="Header-two">
            <span class="Name">{{worker.name}}</span>
            <div class="salary">
                <div>
                    <div>Salario do contrato:</div>
                    <div>Dispesas diarias:</div>
                    <div>Dispesa solicitada:</div>
                    <div>Disconto da empresa:</div>
                </div>
                <div class="money">
                    <span>{{currencyFormat.format(worker.salary)}} <FontAwesomeIcon icon="fa-sack-dollar"/></span>
                    <span>{{currencyFormat.format(worker.totalDispenses)}} <FontAwesomeIcon icon="fa-solid fa-receipt" /></span>
                    <span>{{currencyFormat.format(totalExpenses)}} <FontAwesomeIcon icon="fa-solid fa-money-bill-1-wave"/></span>
                    <span>{{currencyFormat.format(worker.totalExpense)}} <FontAwesomeIcon icon="fa-solid fa-arrow-down" /></span>
                </div>
                <div class="ToReceive">
                    <div>Salario a receber</div>
                    <span>{{currencyFormat.format(worker.totalToReceive)}}</span>
                    <FontAwesomeIcon icon="fa-solid fa-hand-holding-dollar"/>
                </div>
            </div>
        </div>
        <div class="Container">
            <div class="Expenses">
                <span>Dispesas solicitadas</span>
                
                <div class="List" v-for="expense in expenses" :key="expense.id">
                    <div>num: {{expense.id}}</div>
                    <div>Data: {{FormatDate(expense.created_at)}}</div>
                    <div>Tipo de dispesa: {{expense.type}}</div>
                    <div>Total: {{currencyFormat.format(expense.value)}}</div>
                </div>
            </div>
            <div class="Expenses">
                <span>Discontos</span>
                <div class="List" v-for="session in sessions" :key="session.id">
                    <div>num: {{session.id}}</div>
                    <div>Data de abertura: {{FormatDate(session.created_at)}} </div>
                    <div>Data de Fecho: {{FormatDate(session.updated_at)}} </div>
                    <div>{{currencyFormat.format(session.falta)}}</div>
                </div>
            </div>
        </div>
        <div class="Footer">
            <button @click="$emit('close')" class="descartar">Fechar</button>
            <button @click="exportToPDF">imprimir</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import { useStore } from "vuex";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import axios from "axios";
import moment from 'moment'
import html2pdf from "html2pdf.js";

const currencyFormat = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA',})
const props = defineProps({
    worker: Object
})

const worker = ref(props.worker.worker)
const store = useStore()
const company = ref(store.state.Empresa)
const sessions = ref([])
const expenses = ref([])

const totalExpenses = ref(0)

const OnMounted = onMounted(()=>{
    filterSession(worker.value,props.worker.data.session)
    filterExpenses(worker.value,props.worker.data.expenses)
})


const filterSession = ((worker,listSession)=>{
    const filter = listSession.filter((session)=>{
        return session.id_user_responsavel === worker.users_id
    })

    const filterDiscount = filter.filter((session)=>{
        let total = Number(session.total_geral_informado) + Number(session.saida) - Number(session.entrada)
        let test = Number(total - session.total_geral)
        return test < 0
    })

    filterDiscount.forEach((session)=>{
        let total = Number(session.total_geral_informado) + Number(session.saida) - Number(session.entrada)
        session.falta = Number(total - session.total_geral)
        
    })

    if (filter.length > 0) return sessions.value = filterDiscount
})

const filterExpenses = ((worker,listExpenses)=>{
    const filter = listExpenses.filter((expense)=>{
        return expense.funcionarios_id === worker.id
        && expense.type != null
    })
    filter.forEach((filter)=>{
        totalExpenses.value += Number(filter.value)
    })
    if (filter.length > 0) return expenses.value = filter
})

const formatDate = (() =>{
    return moment().format('DD-MM-YYYY H:mm:ss')
 })

 const FormatDate = ((data) =>{
    return moment(data).format('DD-MM-YYYY H:mm:ss')
 })


 const exportToPDF = (()=> {
  const item = document.getElementById("Container");
  var opt = {
    margin: 0,
    filename: `Folha de salario de ${worker.name}`,
    html2canvas: { scale: 2},
  };

  html2pdf().set(opt).from(item).save();
})

</script>

<style lang="scss" scoped>
@import '../../../assets/funcionarios/scss/detailSalary'
</style>

<template>
<ModalExpense :worker="modal.worker" @closeModal="OnMounted" v-if="modal.state == 'solicitar'" @message="message"/>
<DetailSalary @close="modal.state = ''" :worker="modal.worker" v-if="modal.state == 'show'"/>
    <div class="principal">
        <div class="Header">
            <div class="Header-left">
                <span>
                    <!-- <FontAwesomeIcon v-if="FormNewWorker.state" icon="fa-solid fa-outdent" /> -->
                    <h2>Relatorio</h2>
                </span>
            </div>
            <div class="Header-right">
                <span class="p-input-icon-right w-100">
                    <i class="pi pi-search" />
                    <input type="search" @keyup="(e)=>filterWorker(e.target.value)" placeholder="Digite nome ou preço do artigo">
                </span>
            </div>
        </div>
        <div class="Container">
            <div class="list-workers" v-for="worker in workers.workers" :key="worker.id">
                <span><img :src="`/worker/image/${worker.imagem}`" alt=""></span>
                <span>{{worker.name}}</span>
                <span>{{worker.cargo}}</span>
                <span><strong>{{currencyFormat.format(worker.salary)}}</strong></span>
                <span class="dailyExpense">
                    <h3>Dispesa diaria</h3>
                    <div :class="Number(worker.dailyExpense) > 0 ? 'existExpense':''">
                        <strong>{{currencyFormat.format(worker.dailyExpense)}}</strong>
                        <FontAwesomeIcon icon="fa-solid fa-receipt" />
                    </div>
                </span>
                <span class="discount">
                    <div>
                        <h3>Desconto</h3>
                        <FontAwesomeIcon icon="fa-solid fa-tags" />
                    </div>
                    <div :class="Number(worker.dailyExpense) > 0 ? 'existExpense':''" >
                        <strong>{{currencyFormat.format(worker.totalDispenses)}}</strong>
                        <FontAwesomeIcon @mouseenter="drop = worker.id" icon="fa-solid fa-receipt" />
                        <div v-if="drop == worker.id" class="drop">
                            <span @click="AddExpense(worker,'solicitar')">Adicionar</span>
                            <span @click="AddExpense(worker,'show')">Folha de salario</span>
                        </div>
                    </div>
                    <div :class="Number(worker.totalExpense) > 0 ? 'existDiscount':''" >
                        <strong>{{currencyFormat.format(worker.totalExpense)}}</strong>
                        <FontAwesomeIcon icon="fa-solid fa-arrow-down" />
                    </div>
                </span>
                <span class="to-receive">
                    <div :class="Number(worker.dailyExpense) > 0 ? 'existExpense':''">
                        <FontAwesomeIcon icon="fa-solid fa-tags" />
                        <strong>{{currencyFormat.format(worker.totalDispenses)}}</strong>
                    </div>
                    <div :class="Number(worker.dailyExpense) > 0 ? 'existDiscount':''" >
                        <FontAwesomeIcon icon="fa-solid fa-arrow-down" />
                        <strong>{{currencyFormat.format(worker.totalExpense)}}</strong>
                    </div>
                    <div>
                        <h3>A receber</h3>
                        <strong>{{currencyFormat.format(worker.totalToReceive)}}</strong>
                    </div>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { onMounted, ref } from "@vue/runtime-core";
import { useStore } from "vuex";
import ModalExpense from './ModalExpense.vue'
import DetailSalary from './detailSalary.vue'
const drop = ref()
const store = useStore()
const emits = defineEmits(['message'])
const currencyFormat = Intl.NumberFormat("PT-AO",{style:"currency",currency: "AOA"})
const discount = ref(0)
const modal = ref({
    state: "",
    worker: []
})

const workers = ref({
    workers: [],
    storeWorkers: [],
    session: [],
    expenses: []
})

const message = ((message,type)=>{
     emits('message',message,type)
})

const OnMounted = onMounted(()=>{
    drop.value = null
    modal.value.state = ""
    store.state.StateProgress = true
    axios.get('getWorkers')
    .then((response) => {
        workers.value = response.data
        store.state.StateProgress = false
        salarySum()
    }).catch((err) => {
        store.state.StateProgress = false
        message('Aconteceu um erro ao selecionar os funcionario ','info')
        console.log(err);
    });
})

const salarySum = (()=>{
    workers.value.workers.forEach((worker)=>{
        worker.totalExpense = filterExpenses(worker).discount
        worker.totalToReceive = filterExpenses(worker).ToReceive
        worker.totalDispenses = filterExpenses(worker).dispenses
    })
})

const filterWorker = ((type)=>{
    const filter = workers.value.storeWorkers.filter((o)=>{
        return String(o.name).toLowerCase().includes(type.toLowerCase())
        || String(o.department).toLowerCase().includes(type.toLowerCase())
    })
    workers.value.workers = filter
})

const filterExpenses = ((worker)=>{
    const filterExpense = workers.value.expenses.filter((o)=>{
        return o.funcionarios_id == worker.id
    })
    const filterSession = workers.value.session.filter((o)=>{
        return o.id_user_responsavel == worker.users_id
    })

    let valueSession = 0
    let bonus = 0
    filterSession.forEach((session)=>{
        let total = Number(session.total_geral_informado) + Number(session.saida) - Number(session.entrada)
        if (Number(total - session.total_geral)<0) {
            valueSession += Number(total - session.total_geral)
        }else{
            bonus += Number(total - session.total_geral)
        }
    })

    let valueExpense = 0
    let ToReceive = 0
    let dispenses = 0
    let discount = 0
    filterExpense.forEach((expense)=>{
        valueExpense += Number(expense.value)
        dispenses = Number(worker.dailyExpense * 30)
        discount = Number(valueExpense) - Number(worker.dailyExpense) + Number(String(valueSession).replace('-',''))
        ToReceive = Number(worker.salary) - Number(discount) - dispenses
    })

    return {
        dispenses: dispenses,
        discount: discount,
        ToReceive: ToReceive
    }
})

const AddExpense = ((worker,show)=>{
    modal.value.state = show
    if (show == 'show') {
        return  modal.value.worker = {data: workers.value,worker}
    }
    modal.value.worker = worker
})

</script>

<style scoped lang="scss">
@import '../../../assets/funcionarios/scss/salarySum';
</style>

<template>
  <div class="principal">
    <div class="Header">
        <div class="Header-left">
            <span @click="FormNewWorker.state ? OnMounted() : ''" :class="FormNewWorker.state ? 'Voltar' : ''">
                <FontAwesomeIcon v-if="FormNewWorker.state" icon="fa-solid fa-outdent" />
                <h2>Funcionarios</h2>
            </span>
            <span v-if="!FormNewWorker.state">
                <button @click="NewWorker">Novo Funcionario</button>
            </span>
            <span v-else>

                <button @click="SaveWorker">
                    <FontAwesomeIcon icon="fa-solid fa-floppy-disk" />
                    Guardar
                </button>
            </span>
        </div>
        <div class="Header-right">
            <span v-if="!FormNewWorker.state" class="p-input-icon-right w-100">
                <i class="pi pi-search" />
                <input type="search" @keydown="(e)=>filterWorker(e.target.value)" placeholder="Digite nome ou preço do artigo">
            </span>
        </div>
    </div>
    <div v-if="!FormNewWorker.state" class="Container">
        <div class="Types-content">
            <div class="department">
                <img src="/login/image/funcionarios.png" alt="">
                <h3>DEPARTAMENTO</h3>
            </div>
            <div class="list-department">
                <div v-for="department in workers.department" :key="department.id" @click="filterWorker(String(department.id))">
                    <span>{{department.name}}</span>
                    <strong>{{department.functionary}}</strong>
                </div>
            </div>
        </div>
        <div class="list-content">
            <label @click="showWorker(worker)" class="card-worker" v-for="worker in workers.workers" :key="worker.id">
                <div class="image">
                    <img :src="`/worker/image/${worker.imagem}`" :alt="worker.name">
                </div>
                <div class="worker-info">
                    <span class="name">{{worker.name}}</span>
                    <span>{{worker.cargo}}</span>
                    <span>{{worker.email}}</span>
                    <span>{{worker.phone}}</span>
                </div>
            </label>
            <!-- <div v-if="workers.workers.length > 0">

            </div> -->
        </div>
    </div>
    <NewWorkers :worker="Worker" @message="message" v-else/>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from '@vue/runtime-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import NewWorkers from './NewWorker.vue'
import useEventsBus from '@/Eventbus';
import axios from 'axios';
import { useStore } from 'vuex';
const store = useStore()

const emits = defineEmits(['message'])

const {emit,bus} = useEventsBus();

const FormNewWorker = ref({
    state: false,
})

const Worker = ref([])

const workers = ref({
    department: [],
    workers: [],
    storeWorkers: [],
})

const NewWorker = (()=>{
    Worker.value = []
    FormNewWorker.value.state = true
})

const message = ((message,type)=>{
    emits('message',message,type)
})

const SaveWorker = (()=>{
    emit('SaveWorker',+1)
})

const OnMounted = onMounted(()=>{
    store.state.StateProgress = true
    FormNewWorker.value.state = false
    axios.get('getWorkers')
    .then((response) => {
        workers.value = response.data
        FilterWorkers()
        store.state.StateProgress = false
    }).catch((err) => {
        store.state.StateProgress = false
        emits('message','Aconteceu um erro ao selecionar os funcionario ','info')
        console.log(err);
    });
})

watch(()=>bus.value.get('closeFormWorker'),(payload)=>{
    FormNewWorker.value.state = false,
    OnMounted()
})

const showWorker = ((worker)=>{
    Worker.value = worker
    FormNewWorker.value.state = true
})

const FilterWorkers = ((type)=>{

    workers.value.department.forEach((depart)=>{
        const filter = workers.value.workers.filter((o)=>{
            return o.department == depart.id
        })
        depart.functionary = filter.length
    })
})

const filterWorker = ((type)=>{
    const filter = workers.value.storeWorkers.filter((o)=>{
        console.log(String(type));
        return String(o.name).toLowerCase().includes(type.toLowerCase())
        || String(o.email).toLowerCase().includes(type.toLowerCase())
        || String(o.department) == String(type)
    })
    workers.value.workers = filter
})
</script>

<style scoped lang="scss">
@import '../../../assets/funcionarios/scss/menu';
</style>

<template>
<Toast/>
    <div class="mt-0" style="height:100vh;width: 100vw">
        <Headers @modulos="modulos" :menus="menus"/>
        <div class="Containere">
            <Menu @modulos="modulos" @showCompany="showCompany"  
            @ShowSingleUser="ShowSingleUser" v-if="modul.active == 'Configurações'"/>
            <Users @ShowSingleUser="ShowSingleUser"  @message="message" v-if="modul.active == 'users'"/>
            <NewUser @message="message" @Users="modul.active = 'users'"
            v-if="modul.active == 'NewUser'" :SingleUser="SingleUser"/>
            <License @message="message" v-if="modul.active == 'Liseças'" @close="modul.active = modul.story"/>
            <Company @message="message" @close="modul.active = modul.story" v-if="modul.active == 'company'" :company="company"/>
        </div>
    </div>
</template>

<script setup>
import Company from '@/components/config/company.vue'
import Toast from 'primevue/toast'
import Index from '@/layouts/index.vue'
import { onMounted, ref } from '@vue/runtime-core';
import Menu from '@/components/config/menu.vue'
import Users from '@/components/config/users.vue'
import NewUser from '@/components/config/NewUser.vue'
import { useToast } from 'primevue/usetoast';
import License from '@/components/config/license/index.vue'
import { Inertia } from '@inertiajs/inertia';
import Headers from '../layouts/Headers.vue'

const toast = useToast()
const SingleUser = ref(Object)
const MostrarDrop = ref(null)
const company = ref([])
const menus = ref([
    {"menu": "Configurações"},
    {"menu": "Liseças"},
])
const message = ((message,tipo)=>{
    toast.add({
        severity: tipo,
        summary: 'Messagem',
        detail: message,
        life: 5000
    })
})

const modul = ref({
    active: 'Configurações',
    story: '',
})

const Sair = (()=>{
    Inertia.post('/sair');
})

const DropShow = ((evento)=>{
    if (evento == MostrarDrop.value) {
        MostrarDrop.value = null
    } else {
        MostrarDrop.value = evento
    }
})

const modulos = ((event)=>{
    MostrarDrop.value = null
    modul.value.story = modul.value.active
    modul.value.active = event
})

const ShowSingleUser = ((event)=>{
    SingleUser.value = event
    modul.value.story = modul.value.active
    modul.value.active = 'NewUser';
})

const showCompany = ((event)=>{
    company.value = event
    modul.value.story = modul.value.active
    modul.value.active = 'company';
})

</script>

<style lang="scss" scoped>
    .Containere{
        height: 92% !important;
    }
</style>

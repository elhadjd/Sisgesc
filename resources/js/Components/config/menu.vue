<template>
  <div class="MenuPrincipal">
    <div class="Header">
        <div class="HeaderOne">
            <h2>
                Configurações
            </h2>
        </div>
    </div>
    <div class="Container">
        <div class="CardApps">
            <div class="Apps">
                <div class="App" v-for="app in app" :key="app.id">
                    <div class="Imagem">
                        <img :src="'/login/image/'+app.image" >
                    </div>
                    <div class="AppName">
                        {{app.app_name}}
                    </div>

                </div>
            </div>
            <div class="Footer">
                <span class="TotalApps">
                    {{app.length+' Modulos Instalados'}}
                </span>
                <span class="AddApp">Adicionar Modulo</span>
            </div>

        </div>
        <div class="CardApps">
            <div class="Apps">
                <div class="App" @click="ShowUserSingle(user)" v-for="user in users.slice(0,6)" :key="user.id">
                    <div class="Imagem">
                        <img :src="'/login/image/'+user.imagem" >
                    </div>
                    <div class="AppName">
                        {{user.apelido}}
                    </div>
                </div>
            </div>
            <div class="Footer">
                <span class="TotalApps">
                    {{users.length+' Usuario Conectado'}}
                </span>
                <span @click="$emit('modulos','users')" class="AddApp">Lesta de Usuarios</span>
            </div>
        </div>
        <div class="CardApps">
            <div class="content">
                <div @click="showCompany('company')" class="company">
                    <div class="Imagem">
                        <img :src="'/login/image/'+company.imagem" >
                    </div>
                    <div class="name">
                        {{company.nome_de_empresa}}
                    </div>
                </div>
                <div class="version">

                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core";
import axios from "axios";
import { useStore } from "vuex";
const store = useStore()
const app = store.state.Apps
const users = ref([])
const emits = defineEmits('ShowSingleUser')
const company = ref([])

const Onmounted = onMounted(()=>{
    axios.get('/users')
    .then((Response) => {
        users.value = Response.data
        getCompany()
    }).catch((err) => {
        console.log(err);
    });
})

const getCompany = (()=>{
    axios.get('/getCompany')
    .then((response) => {
        company.value = response.data
    }).catch((err) => {
        console.log(err);
    });
})

const ShowUserSingle = ((event)=>{
    emits('ShowSingleUser',event)
})

const showCompany = (()=>{
    emits('showCompany',company.value)
})
</script>

<style lang="scss" scoped>
@import '../../../assets/config/scss/menu';
</style>

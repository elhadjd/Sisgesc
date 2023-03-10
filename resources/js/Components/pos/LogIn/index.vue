<template>
<div class="Principal">
    <div class="Formulario">
        <div class="Header">
            <h1>{{caixa.nome}}</h1>
            <div>
                <img src="/produtos/image/arro.png" alt="">
            </div>

        </div>
        <div class="Container">
            <input @keypress.enter="Entrar" type="password" v-model="EnviarCaixa.password">
        </div>
        <div class="Footer">
            <Link :href="route('SairPos')" class="sair">Voltar</Link>
            <button class="entrar" @click="Entrar">Entrar</button>
        </div>
    </div>
</div>
</template>

<script setup>
import useEventsBus from "@/Eventbus"
import { Link } from "@inertiajs/inertia-vue3"
import { onMounted, ref } from "@vue/runtime-core"
import axios from "axios"
import { useStore } from "vuex"

const {emit} = useEventsBus();
const emits = defineEmits(['LogIn','message'])

const props = defineProps({
    Cash: Object,
})

const store = useStore()

const caixa = ref([])

const EnviarCaixa = ref({
    id: null,
    password: null
})

const Entrar = (()=>{
    if (EnviarCaixa.value.password === null || EnviarCaixa.value.password === '') {
        emits('message','error','Por favor preencho o campo')
    } else {
        if (EnviarCaixa.value.id === null || EnviarCaixa.value.id === '') {
            emits('message','error','Erro no sistema por favor atualize a tua pagina e tenta novamente')
        }else{
            axios.post(`/PasswordCash/${EnviarCaixa.value.id}/check`,{password:EnviarCaixa.value.password,})
            .then((Response) => {
                emits('message',Response.data.tipo,Response.data.messagem)
                if (Response.data.tipo == 'success') {
                    store.state.Controlo.estado = true
                    emit('Logado');
                    return OnMounted()
                }
            }).catch((err) => {
                console.log(err);
            });
        }
    }
})

const OnMounted = onMounted(async ()=>{
    caixa.value = props.Cash
    EnviarCaixa.value.id = caixa.value.id
    if (caixa.value.password != null || caixa.value.password !='') {
        if (store.state.Controlo.estado === true) {
            emits('LogIn',true)
        } else {
            emits('LogIn',false)
        }
    } else {
        emits('LogIn',true)
    }
})
</script>

<style lang="scss" scoped>
@import '../../../../assets/Pos/css/LogIn/index'
</style>

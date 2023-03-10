<template>
     <div class="principal">
    <div class="form-container">
        <div class="Header">
            <button>
                <FontAwesomeIcon icon="fa-solid fa-users"/>
                <div>
                    <span></span>
                    <span>Funcionarios</span>
                </div>
            </button>
        </div>
        <div class="Container">
            <div class="Name-Img-control">
                <div class="form-nome">
                    <input type="text" v-model="armagen.NomeArmagen" placeholder="Nome do armagen">
                </div>
            </div>
            <div class="info-basic">
                <div class="form-content">
                    <div class="form-Control">
                        <label for="city">Cidade:</label>
                        <input type="text" v-model="armagen.Cidade" placeholder="Informe a sua cidade" id="city">
                    </div>
                    <div class="form-Control">
                        <label for="bairo">Bairo:</label>
                        <input type="text" v-model="armagen.Bairo" placeholder="Informe o seu bairo" id="bairo">
                    </div>
                    <div class="form-Control">
                        <label for="numCasa">Numero de casa:</label>
                        <input type="text" v-model="armagen.NumeroCasa" placeholder="Numero de casa" id="numCasa">
                    </div>
                </div>
            </div>
        </div>
        <div class="Footer">

        </div>
    </div>
  </div>
</template>

<script setup>
import useEventsBus from "@/Eventbus";
import { onMounted, ref, watch } from "@vue/runtime-core";
import axios from "axios";

const {bus} = useEventsBus()
const emits = defineEmits(['message','closeForm'])
const props = defineProps({
    armagen: Array
})
const armagen = ref({
    id: "",
    NomeArmagen: null,
    Cidade: null,
    Bairo: null,
    NumeroCasa: null
})

onMounted(()=>{
    if (props.armagen.id) {
        armagen.value = props.armagen
    }
})

watch(()=>bus.value.get('saveArmagen'),(payload)=>{
    saveArmagen(armagen.value);
})

const saveArmagen = ((armagen)=>{
    if (armagen.NomeArmagen == "" || armagen.NomeArmagen == null) return message('Preencha os campos','info')
    axios.post('/saveArmagen',{...armagen})
    .then((response) => {
        emits('message','Sucesso','success')
        emits('closeForm')
    }).catch((err) => {
        console.log(err);
    });
})

const message = ((message,type)=>{
    emits('message',message,type)
})
</script>

<style scoped lang="scss">
.principal{
    background-color: #f9f9f9f9;
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    justify-content: center;
    align-items: center;
    overflow-y: auto;
    @include scroll;
    @include formulary
}
</style>

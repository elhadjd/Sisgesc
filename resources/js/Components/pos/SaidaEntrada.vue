<template>
<Transition>
<div class="principal">
    <div class="Modal">
        <div class="Header">
            <div class="Botoes">
                <button :class="TipoButton.tipo == 'Entrada' ? 'style' : ''" @click="TipoOperacao('Entrada')">Entrada</button>
                <button :class="TipoButton.tipo == 'Saida' ? 'style' : ''" @click="TipoOperacao('Saida')">Saida</button>
                <button :class="TipoButton.tipo == 'Gasto' ? 'style' : ''" @click="TipoOperacao('Gasto')">Gasto</button>
            </div>
            <div class="Input">
                <input type="text" id="inputRef" ref="inputRef" v-model="TipoButton.valor" placeholder="Digite valor">
            </div>
        </div>
        <div class="Container">
            <textarea v-model="TipoButton.assunto" cols="30" rows="10"></textarea>
        </div>
        <div class="Footer">
            <button @click="$emit('fechar')" class="Fechar">Fechar</button>
            <button @click="Save" class="Guardar">Guardar</button>
        </div>
    </div>
</div>
</Transition>
</template>

<script setup>
import { onMounted,ref } from '@vue/runtime-core'
import axios from 'axios'
import { useCurrencyInput } from 'vue-currency-input'
const FormatarDineiro = Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

const emits = defineEmits(['message','fechar'])

const { inputRef } = useCurrencyInput({currency: 'AOA' })

const TipoButton = ref({
    id: null,
    tipo: null,
    valor: null,
    assunto: null
})

const TipoOperacao = ((event)=>{
    TipoButton.value.tipo = event
    inputRef.value.focus
})

const Save = (()=>{
    if ((TipoButton.value.tipo == null)||(TipoButton.value.valor == null)||(TipoButton.value.assunto == null)||(TipoButton.value.valor == '')||(TipoButton.value.assunto == '')) {
        emits('message','info','Atenção os campos não podem ficar vazios')
    } else{
        axios.post('/EntradaSaida',{Guardar:TipoButton.value})
        .then((Response) => {
            emits('message',Response.data.tipo,Response.data.message)
            emits('fechar')
        }).catch((err) => {
            console.log(err);
        });
    }
})

onMounted(()=>{
})

</script>

<style lang="scss" scoped>
    @import '../../../assets/Pos/css/EntradaSaida';
</style>

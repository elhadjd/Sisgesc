<template>
<Toast />
<Dialog class="p-0" :header="props.dados.tipo + ' em Stock'" v-model:visible="displayPosition" :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '50vw'}" :position="position" :modal="true">
    <div class="w-100 h-auto bg-white">
        <form @submit.prevent.stop="Confirmar">
            <div class="p-2">
                <div class="w-100 p-1 d-flex text-secondary h5">
                    <label for="" class="w-50"><strong>Artigo</strong></label>
                    <label v-if="form.tipoOper != 'Entrada'" for="" class="w-50 text-center"><strong>Motivo</strong></label>
                </div>
                <div class="d-flex">
                    <div class="w-50 p-1">{{product.nome}}</div>
                    <textarea class="InputMotif" v-if="form.tipoOper != 'Entrada'" type="text" v-model="form.motif"></textarea>
                </div>
                <div class="d-flex mt-4 p-2 text-secondary w-100 border-bottom">
                    <div class=" w-25 d-flex">
                        <div class=""><strong>{{product.qtd+",00Un(s)"}}</strong></div>
                        <div class="text-right"><i class="fa fa-bar-chart"></i></div>
                    </div>
                    <div class="w-75 d-flex">
                        <strong class="mt">Quantidade</strong>
                        <InputNumber class="quantidade py-0 ms-2 w-75" inputId="locale-user" v-model="form.quantidade" mode="decimal" :minFractionDigits="2" />
                    </div>
                </div>
                <div class="d-flex w-100 mt-2">
                    <div @click="$emit('fechar')" class="ms-1 fechar">Fechar</div>
                    <div class=" ms-1 ConfirmarStock">
                        <div v-if="ShowModal"><i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i></div>
                        <button v-else>Confirmar</button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</Dialog>
</template>

<script setup>
import InputNumber from 'primevue/inputnumber'
import Dialog from 'primevue/dialog'
import { onMounted, reactive, ref } from '@vue/runtime-core';
import { useToast } from "primevue/usetoast";
import axios from 'axios';

const toast = useToast()

const position = ref();

const displayPosition = ref();

const ShowModal = ref(false)

const emits = defineEmits(['Confirmar','fechar'])

const props = defineProps({
    dados: Object
})


const product = ref([])

onMounted(() => {
    displayPosition.value = true
    position.value = 'top'
    product.value = props.dados.product
})

const form = reactive({
    quantidade: 0,
    idArtigo: product.value.id,
    tipoOper: props.dados.tipo,
    motif: String()
})

const Confirmar = (()=>{
    if (form.quantidade != 0) {
        // if (form.tipoOper != 'Entrada') {
        //     if (form.motif == '' || form.motif.length <=11) {
        //         return toast.add({severity:'info',
        //             summary: 'menssagem',
        //             detail:'Campo motivo é obligatorio e ten de conter no minimo 10 digito',
        //             life: 5000
        //         });
        //     }
        // }
        form.idArtigo = product.value.id
        ShowModal.value = true;
        axios.post('/EntradaSaidaStock',
            form
        ).then((Response) => {
            if (!Response.data.message) {
                if (form.tipoOper == 'Entrada') {
                    product.value.qtd += form.quantidade
                } else {
                    product.value.qtd -= form.quantidade
                }
                emits('fechar')
                emits('Confirmar')
                toast.add({severity:'success', summary: 'menssagem', detail:'Operação concluida', life: 5000});
            }else{
                toast.add({severity:'info', summary: 'menssagem', detail:Response.data.message, life: 5000});
            }
            ShowModal.value = false;
        }).catch((err) => {
            console.log(err);
        });
    } else {
        toast.add({severity:'info', summary: 'menssagem', detail:'A campo quantidade não pode ficar vazio', life: 5000});
    }
})

</script>

<style scoped lang="scss">
*::before,
*::after {
  box-sizing: border-box;
}
.p-dialog-content{
    padding: 0px !important;
}
.p-dialog-header{
    padding: 0px 0px 0px 10px !important;
}
.fechar{
    border: 1px solid white;
    color: #017e84;
    padding: 3px 10px 3px 10px;
    font: 10pt arial;
    font-weight: 600;
    &:hover{
        border: 1px solid #017e84;
        cursor: pointer;
    }
}
.ConfirmarStock{
    @include botao_normal;
    i{
        font-size: 15px;
    }
}
.quantidade{
    border: none !important;
    outline: none !important;
    border-bottom: 1px solid #017e84;
}
.InputMotif{
    width: 46% !important;
    height: 40px;
    max-height: 40px;
    border: none;
    outline: none !important;
    box-shadow: 0 0 0 0;
    border: 1px solid #017e84;
    padding: 5px;
	font-family: Tahoma, sans-serif;
	background-image: url(bg.gif);
	background-position: bottom right;
	background-repeat: no-repeat;
}
input{
    box-shadow: 0 0 0 0 !important;
    border: 0 none !important;
    border-bottom: 1px solid #017e84 !important;
    outline: 0 !important;
    padding: 0px 10px 0px 10px !important;
    font-style: arial;
    font-weight: 600 !important;
    border-radius: 0px !important;
    &:focus {
        box-shadow: 0 0 0 0 !important;
        border: 0 none !important;
        border-bottom: 1px solid #017e84 !important;
        outline: 0 !important;
    }
}
</style>

<template>
<Toast></Toast>
  <div class="Principal">
    <div class="Modal">
        <div class="Header">
            <h2>NOVA CATEGORIA</h2>
            <div class="ProductsSingleCategoria">
                <i class="fa fa-product-hunt"></i>
                <span>Produtos</span>
            </div>
        </div>
        <div class="Container">
            <input type="text" v-model="Categoria.nome" placeholder="Novo Categoria">
        </div>
        <div class="Footer">
            <button @click="$emit('FecharNewCateg')" class="Descartar">Fechar</button>
            <button @click="GuardarCategoria">Guardar</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from "@vue/runtime-core";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import Toast from 'primevue/toast';

const emit = defineEmits(['FecharNewCateg']);
const props = defineProps({
    categoria:Object
});

const toast = useToast();

const Categoria = reactive({
    nome: null,
})

const GuardarCategoria = (()=>{
    axios.post(`/Categoria/${props.categoria.idProd}`,Categoria).then((response) => {
        toast.add({
            severity: response.data.tipo,
            summary:  'Mensagem',
            detail: response.data.message,
            life:5000
        });

        emit('FecharNewCateg');
        
    }).catch((erro) => {
        console.log(erro)
    });
})
</script>

<style lang="scss" scoped>
    .Principal{
        @include modal;
        background-color: rgba(0, 0, 0, 0.46) !important;
        align-items: center !important;
        .Modal{
            .Header{
                padding: 0px !important;
                justify-content: space-between;
                h2{
                    padding: 10px;
                }
                .ProductsSingleCategoria{
                    width: 120px;
                    height: 100%;
                    padding: 10px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-left: 1px solid #ddd;
                    color: #017e84;
                    &:hover{
                        cursor: pointer;
                        background-color: #017e8444;
                        color: #017e84;
                    }
                }
            }
            .Container{
                input{
                    @include InputPrincipal;
                    width: 100% !important;
                }
            }
        }
        
    }
</style>
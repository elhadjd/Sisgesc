<template>
<Transition name="bounce">
  <div class="Principal">
    <div class="Form">
        <div class="Header">
            <h3>Informação do produto</h3>
        </div>
        <div class="Container">
            <div class="Info-One">
                <div>
                    <h4>{{dados.nome}}</h4>
                </div>
                <div>
                    <h4 class="Money">{{FormatarDineiro.format(dados.preçovenda)}}</h4>
                    <span>isento de IVA-Resumo de exclusão</span>
                </div>
            </div>
            <div class="Info-two">
                <div>
                    <div class="Title">
                        <h4>Financial</h4>
                        <hr WIDTH=100%/>
                    </div>
                </div>
                <div class="Finanças">
                    <div class="Finanças-One">
                        <div>
                            <span>Venda</span>
                            <span>Custo</span>
                            <span>Margin</span>
                        </div>
                        <div>
                            <span>{{FormatarDineiro.format(dados.preçovenda)}}</span>
                            <span>{{FormatarDineiro.format(dados.preçocust)}}</span>
                            <span>{{FormatarDineiro.format(dados.preçovenda - dados.preçocust)}}</span>
                        </div>
                    </div>
                    <div class="Finanças-Two">

                    </div>
                </div>
            </div>
            <div class="Info-tree">
                <div>
                    <div class="Title">
                        <h4>Inventario</h4>
                        <hr WIDTH=100%/>
                    </div>
                </div>
                <div class="Inventario">
                    <div>
                        <span>Stock disponivel</span>
                        <span>{{dados.qtd+',00Unidade(s)'}}</span>
                    </div>

                </div>
            </div>
            <div class="Info-for">
                <div>
                    <div class="Title">
                        <h4>Pedido</h4>
                        <hr WIDTH=100%/>
                    </div>
                    <span></span>
                </div>
            </div>
        </div>
        <div class="Footer">
            <button @click="$emit('CloseInfo')">Fechar</button>
        </div>
    </div>
  </div>
</Transition>
</template>

<script setup>
import { onMounted, ref } from "@vue/runtime-core"

const dados = ref([])

const props = defineProps({
    Information: Object
})

const FormatarDineiro = new Intl.NumberFormat('PT-AO',{style: 'currency',currency: 'AOA'})

onMounted(()=>{
    dados.value = props.Information
})

const open = ref(false)
</script>

<style scoped lang="scss">
@import '../../../assets/Pos/css/InfoProd.scss';
</style>

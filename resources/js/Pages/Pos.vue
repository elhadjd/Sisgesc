<template>
  <MenuPos :Cash="props.caixa"/>
</template>

<script setup>
import MenuPos from '@/components/pos/menuPos.vue'
import useEventsBus from '@/Eventbus'
import { onMounted,watch } from 'vue'
import { useStore } from 'vuex';

const store = useStore();
const {bus} = useEventsBus();

watch(()=>bus.value.get('EncerrarSessao'), (payload) => {
    store.commit('CloseCash',false)
})

const props = defineProps({
    caixa: Object
})

onMounted(()=>{
    axios.get(`/getCashConfig/${props.caixa.id}`)
    .then((response) => {
        store.commit("StoreConfigCash",response.data);
    })
})
</script>


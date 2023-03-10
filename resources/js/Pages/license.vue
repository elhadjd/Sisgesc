<template>
  <div class="License-container">
    <Toast v-if="!props.license"/>
    <License :messagem="props.message" v-else/>
  </div>
</template>

<script setup>
import { onMounted } from '@vue/runtime-core'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import License from '../components/config/license/index.vue'
const toast = useToast()
const props = defineProps({
    license: Boolean,
    message: String
})

onMounted(()=>{
    if (!props.license) {
        toast.add({
            severity: 'info',
            summary: 'Menssagem',
            detail: 'Por favor acerta a hora e a data do seu computador e tenta novamente '
        })
    }
})

</script>

<style>
    .License-container{
        width: 100vw;
        height: 100vh;
    }
</style>

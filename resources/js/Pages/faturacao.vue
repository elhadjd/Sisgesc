<template>
  <div class="MenuFaturacao bg-white">
    <Menu :dados="props"/>
  </div>
</template>
<script setup>
import { onMounted , ref} from '@vue/runtime-core';
import Menu from "../components/faturacao/menu.vue";
import ModalConfirm from "../components/faturacao/ModalConfirm.vue";
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
}
.MenuFaturacao {
  width: 100vw;
  height: 100vh;
}
</style>

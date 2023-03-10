<template>
<Toast/>
  <div style="height: 100%">
    <div v-if="mostrarModalPag == true">
      <FormPagamento @FecharModalPag="Pagament"/>
    </div>
    <div style="height: 100%">
        <Header :dados="props.dados" @titulo="titulo" style="height: 6.5%" />
        <div class="TabelaBaixo">
            <Pagamento @message="message" v-if="form_faturacao === 'Pagamentos'" />
            <Produtos @message="message" v-if="form_faturacao === 'Produtos'" />
            <Crearfatura :Invoice="Invoice" @message="message" v-if="form_faturacao === 'NovaFatura'" @CancelarFatura="titulo('Ordens')" @Pagamento="Pagament"/>
            <Listarfaturas @message="message" @fatura="ShowInvoice" v-if="form_faturacao === 'Ordens'" />
            <Clientes @message="message" v-if="form_faturacao === 'Clientes'"/>
            <Fornecedores @message="message" v-if="form_faturacao === 'Fornecedores'"/>
        </div>
    </div>
  </div>
</template>

<script setup>
import Toast from 'primevue/toast'
import Clientes from '../Clientes/index.vue'
import Header from "./header.vue";
import { ref, defineProps, onMounted } from "vue";
import Crearfatura from "./crearfatura.vue";
import Listarfaturas from "./ListarFaturas.vue";
import Produtos from "@/components/produtos/produtos.vue";
import Pagamento from "./pagamento/index.vue";
import FormPagamento from "./pagamento/FormPagamento.vue";
import Fornecedores from '@/components/fornecedores/index.vue'
import { useToast } from 'primevue/usetoast';

const toast = useToast()

const props = defineProps({ dados: Object });

const form_faturacao = ref("Ordens");

const mostrarModalPag = ref(false)

const Invoice = ref([])

const Pagament = (()=>{
    mostrarModalPag.value =! mostrarModalPag.value
})

const titulo = (event) => {
  form_faturacao.value = event;
};

const ShowInvoice = ((fatura)=>{
    Invoice.value = fatura
    form_faturacao.value = 'NovaFatura'
})

const message = ((message,tipo)=>{
    toast.add({
        severity: tipo,
        summary: 'Message',
        detail: message,
        life: 5000,
    })
})

</script>

<style>
.TableFaturas {
  width: 100vw;
}
.Filtrar {
  width: 15%;
}
.Pesquisa {
  width: 85%;
}
.TabelaBaixo {
  height: 93%;
  overflow-y: auto;
}
.menssagens {
  position: absolute;
  right: 0;
  max-width: 50%;
}
</style>

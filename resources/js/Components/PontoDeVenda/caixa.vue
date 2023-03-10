<template>
    <Toasts/>
    <Progress v-if="ShowModal"/>
  <div class="principal">
    <div class="Header"></div>
    <div class="Container">
      <div class="Botoes">
        <button @click="AbrirControlo" v-if="DadosCaixaSession.estado == 'A abrir'" class="botoesCaixa">
          Abrir controlo
        </button>
        <button
          @click="ContinuarVenda"
          v-if="DadosCaixaSession.estado == 'Aberto'"
          class="BtnColor Continuar botoesCaixa"
        >
          Continuar
        </button>
        <button @click="CloseCash" v-if="DadosCaixaSession.estado == 'Aberto'" class="mx-1 botoesCaixa">
          Fechar
        </button>
        <button @click="EditarSession" v-if="DadosCaixaSession.estado == 'Fechado'" class="mx-1 botoesCaixa">
          Editar
        </button>
      </div>
      <div class="FormCaixaCompleta">
        <div class="FormCaixa">
          <div class="p-0" id="FormSession">
            <div class="d-flex FormSessionCima">
                <div class="d-flex">
                    <cash class="mt-2" :size="25"/>
                    <div class="TotalPorCima">
                        <div class="text-center">{{FormatrDinheiro.format(DadosCaixa.entrada)}}</div>
                        <div>Entrada de caixa</div>
                    </div>
                </div>
                <div class="d-flex">
                    <cash class="mt-2" :size="25"/>
                    <div class="TotalPorCima">
                        <div class="text-center">{{FormatrDinheiro.format(DadosCaixa.saida)}}</div>
                        <div>Saida de caixa</div>
                    </div>
                </div>
                <div class="d-flex">
                    <cash class="mt-2" :size="25"/>
                    <div class="TotalPorCima">
                        <div class="text-center">{{FormatrDinheiro.format(DadosCaixaSession.total_geral_informado)}}</div>
                        <div>Valor informado</div>
                    </div>
                </div>
                <div class="d-flex">
                    <cash class="mt-2" :size="25"/>
                    <div class="TotalPorCima">
                        <div>{{FormatrDinheiro.format(DadosCaixa.diferencia)}}</div>
                        <div>Diferencia</div>
                    </div>
                </div>
                <div class="d-flex">
                    <shopping class="mt-2" :size="25"/>
                    <div class="TotalPorCima">
                    <div>{{DadosCaixa.pedidos}}</div>
                    <div>Pedidos</div>
                    </div>
                </div>
                <div class="d-flex">
                    <Coin class="mt-2" :size="25"/>
                    <div class="TotalPorCima">
                        <div class="text-center">{{FormatrDinheiro.format(DadosCaixa.totalGeral)}}</div>
                        <div>Pagamentos</div>
                    </div>
                </div>
            </div>


            <div class="content">
              <div class="title_input">
                <div class="titulo">
                  <h1>POS/0000{{DadosCaixaSession.id}}</h1>
                </div>

                <div v-if="DadosCaixaSession.estado !='Fechado'" class="DivInput">
                  <input type="number" class="Input" v-model="Input" :placeholder="
                  DadosCaixaSession.estado == 'Aberto'?'Digite o valor de fachamento':placeholder
                  "/>
                </div>
              </div>

              <div class="content-two">
                <div class="informacoes">
                  <div>
                    <h2>Aberto por</h2>
                    <h2>Ponto de Venda</h2>
                    <h2>Data de Abertura</h2>
                    <h2  v-if="DadosCaixaSession.estado =='Fechado'">Data de Fecho</h2>
                  </div>

                  <div>
                    <h3>{{ DadosCaixaSession.apelido }}</h3>
                    <h3>{{ DadosCaixaSession.nome }}</h3>
                    <h3>{{ DadosCaixaSession.created_at }}</h3>
                    <h3  v-if="DadosCaixaSession.estado =='Fechado'">{{ DadosCaixaSession.updated_at }}</h3>
                  </div>
                </div>

                <div v-if="DadosCaixaSession.estado != 'A abrir'" class="abertura">
                  <div class="informacoes">
                    <div>
                      <h2>Abertura</h2>
                      <h2>Numerario</h2>
                      <h2>Multicaixa</h2>
                      <h2>Transferencia</h2>
                    </div>

                    <div>
                      <h3>{{ FormatrDinheiro.format(DadosCaixaSession.saldo_de_abertura) }}</h3>
                      <h3>{{ FormatrDinheiro.format(DadosCaixa.numerario) }}</h3>
                      <h3>{{ FormatrDinheiro.format(DadosCaixa.multicaixa) }}</h3>
                      <h3>{{ FormatrDinheiro.format(DadosCaixa.transferencia) }}</h3>
                    </div>
                  </div>

                </div>

              </div>
            </div>
          </div>
        </div>
      </div> 
    </div> 
  </div>
</template>

<script setup>
import Toasts from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import cash from 'vue-material-design-icons/CashMultiple.vue'
import shopping from 'vue-material-design-icons/Shopping.vue'
import Coin from 'vue-material-design-icons/HandCoinOutline.vue'
import Progress from '../confirmation/progress.vue'
import { onMounted, reactive, toRefs, ref ,defineProps, watch, onUpdated} from "@vue/runtime-core";
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import Message from 'primevue/message'

  const emits = defineEmits(['message'])

    const ShowModal = ref(false)

    const props =  defineProps(['caixaId']);

    const DadosCaixa = ref([]);

    const DadosCaixaSession = ref([]);

    const Toast = useToast()

    const Input = ref()


    const placeholder = ref('digita o valor de abertura')

    const FormatrDinheiro = new Intl.NumberFormat('PT-AO',{style: 'currency', currency: 'AOA'})

    const ContinuarVenda = (()=>{
        Inertia.post('/HeaderPos',{id_da_caixa:DadosCaixaSession.value.id_da_caixa},{
            onSuccess: ((Response) => {
            }),
        })
    })



    const AbrirControlo = () => {
        if (Input.value == null || Input.value == '') {
            Toast.add({
                severity: 'info',
                summary: 'Menssage',
                detail: 'Por favor informe o valor para Abrir a caixa',
                life: 5000,
            })
        } else {
            ShowModal.value = true;
            DadosCaixaSession.value.saldo_de_abertura = Input.value
            axios.post('/AbrirControloCaixa',DadosCaixaSession.value)
            .then((response)=>{
              if (response.data.message) {
                return emits('message',response.data.message,response.data.tipo)
              }
                DadosCaixa.value = response.data;
                DadosCaixaSession.value = response.data.session;
                ShowModal.value = false;
                localStorage.clear()
            }).catch((erro)=>{
                console.log(erro);
            })
        }
        
    }

    const EditarSession = (()=>{
        ShowModal.value = true;
        axios.post('/EditarSession',DadosCaixaSession.value)
        .then((response)=>{
            DadosCaixa.value = response.data;
            DadosCaixaSession.value = response.data.session;
            CalcularValorCaixa()
            ShowModal.value = false;
        }).catch((erro)=>{
            console.log(erro);
        })
    })

    const CloseCash = (()=>{
        // A verificar se usuario informou o valor do fecho
        if (Input.value == null || Input.value == '') {
            Toast.add({
                severity: 'info',
                summary: 'Menssage',
                detail: 'Por favor informe o valor para fechar a caixa',
                life: 5000,
                })
        } else {
            ShowModal.value = true;
            DadosCaixaSession.value.total_geral_informado = Input.value
            axios.post('/FecharSession',DadosCaixaSession.value)
            .then((response)=>{
                DadosCaixa.value = response.data;
                DadosCaixaSession.value = response.data.session;
                CalcularValorCaixa()
                ShowModal.value = false
            }).catch((erro)=>{
                console.log(erro);
            })
        }
    })

    onMounted(async () => {
         await axios.get('/SessoesCaixas?id='+props.caixaId)
        .then((Response) => {
            DadosCaixa.value = Response.data
            DadosCaixaSession.value = Response.data.session
            CalcularValorCaixa()
        }).catch((err) => {
            console.log(err);
        });
    });

    const CalcularValorCaixa = (()=>{
        DadosCaixa.value.totalGeral += DadosCaixa.value.entrada - DadosCaixa.value.saida
        DadosCaixa.value.diferencia  =DadosCaixaSession.value.total_geral_informado - DadosCaixa.value.totalGeral
    })

</script>

<style lang="scss" scoped>
@import "../../../assets/PontoVenda/css/caixa";
</style>

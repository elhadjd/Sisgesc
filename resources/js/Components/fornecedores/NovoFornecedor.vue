<template>
  <div class="criarClientes">
    <Toast/>
    <Confirmation v-if="confirm" :SmsConfirm="SmsConfirm" @Confirme="Confirme"/>
    <div class="PretedFornecedor" v-if="PretedFornecedor != null">
        <ProdutosFornecedor @Fechar="OnMounted" :IdFornecedor="DadosFornecedor.id" v-if="PretedFornecedor == 'produtos'"/>
    </div>
    <div v-else class="formulario">
        <div class="formulario-header">
            <div class="card-header Action dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                Ação
            </div>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <li v-if="DadosFornecedor.estado == 0" @click="Desarquivar"><a class="dropdown-item" href="#">Desarquivar</a></li>
                <li v-else @click="Arquivar"><a class="dropdown-item" href="#">Arquivar</a></li>
                <li @click="Apagar"><a class="dropdown-item" href="#">Apagar</a></li>
            </ul>
            <div class="card-header">
                <font-awesome-icon icon="fa-solid fa-cart-shopping" />
                <span>
                    <strong>{{PretendFornecedor.EncomendasLista.length}}</strong>
                    Encomendas
                </span>
            </div>
            <div class="card-header" @click="produtos('produtos')">
                <font-awesome-icon icon="fa-brands fa-product-hunt" />
                <span>
                    <strong>{{PretendFornecedor.ProdutosLista.length}}</strong>
                    Produtos
                </span>
            </div>
            <div class="card-header">
                <font-awesome-icon icon="fa-solid fa-money-check-dollar" />
                <span>
                    <strong>{{PretendFornecedor.PagamentosLista.length}}</strong>
                    Pagamentos
                </span>
            </div>
        </div>

        <div class="formulario-content">
            <div class="imagem">
                <div class="nomeCliente">
                    <input class="Nome" :disabled="DadosFornecedor.estado == 0" type="text" v-model="DadosFornecedor.nome" placeholder="digita o nome do fornecedor"/>
                    <input class="Empressa" placeholder="Empresa" type="text" v-model="DadosFornecedor.empresa"/>
                </div>

                <div class="img">
                       <div class="Arquivado" v-if="DadosFornecedor.estado == 0">
                            <h2>ARQUIVADO</h2>
                        </div>
                        <div v-else class="icones">
                            <label for="imagem" id="edit_img">
                                <input type="file" @change="onFileChange" ref="fileInput" id="imagem" class="position-absolute d-none"/>
                                <i class="fa fa-pencil-square-o EditarImagen" aria-hidden="true" ></i>
                            </label>
                            <span @click="DeletarImagem" id="icone_delete_img">
                                <i class="fa fa-ban" aria-hidden="true"></i>
                            </span>
                        </div>
                    <div class="imagem-cliente">
                        <img :src="element.imagen"/>
                    </div>
                </div>

            </div>

            <div class="formulario-insert">
                <div class="Formulario-one">
                    <div class="dados">

                        <div class="dados-one">
                            <div>Nif</div>
                            <div>Telefone</div>
                            <div>Email</div>
                        </div>

                        <div class="dados-two">
                            <input :disabled="DadosFornecedor.estado == 0" type="text" v-model="DadosFornecedor.nif" placeholder="NIF"/>
                            <input :disabled="DadosFornecedor.estado == 0" type="text" v-model="DadosFornecedor.telefone" placeholder="Telefone"/>
                            <input :disabled="DadosFornecedor.estado == 0" type="text" v-model="DadosFornecedor.email" placeholder="Email"/>
                        </div>
                    </div>

                    <div class="endereco">
                        <div class="EnderecoTitl">
                            <div>Pais</div>
                            <div>cidade</div>
                            <div>Sede</div>
                        </div>

                        <div class="EnderecoContent">
                            <input :disabled="DadosFornecedor.estado == 0" type="text" @click="SelectPais" v-model="DadosFornecedor.pais" placeholder="Pais"/>
                            <input :disabled="DadosFornecedor.estado == 0" type="text" v-model="DadosFornecedor.cidade" placeholder="Cidade"/>
                            <input :disabled="DadosFornecedor.estado == 0" type="text" v-model="DadosFornecedor.sede" placeholder="sede"/>
                        </div>
                    </div>
                </div>

                <div>
                    em processo...
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import Confirmation from '@/components/confirmation/index.vue'
import { onMounted, ref, reactive, watch ,defineProps, computed} from "vue";
import { useStore } from "vuex";
import Confirm from '@/components/confirmation/confirm.vue'
import ProdutosFornecedor from './ProdutosFornecedor.vue'
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import axios from 'axios';

const PretedFornecedor = ref(null)

const emits = defineEmits(['voltar'])

const toaste = useToast()

const EnviarDados = ref([])

const SmsConfirm = ref()

const confirm = ref(false)

const props = defineProps({
    Guardar: Boolean,
    Dados: Object
});

const element = reactive({
  imagen: "clientes/image/registro-sem-imagen.png",
});

const Arquivar = (()=>{
    DadosFornecedor.value.estado = 0;
})

const Apagar = (()=>{
    confirm.value = true
    SmsConfirm.value = 'apagar'
})

const store = useStore()

const DadosFornecedor = ref({
    nome: null,
    empresa: null,
    imagem: null,
    telefone: null,
    email: null,
    pais: null,
    cidade: null,
    sede: null,
    id: null,
    estado: 1,
});

const produtos = ((event)=>{
    PretedFornecedor.value = event
})

const PretendFornecedor = ref({
    ProdutosLista: [],
    EncomendasLista: [],
    PagamentosLista: [],
})

const OnMounted = onMounted(async ()=>{
    if (props.Dados != null) {
        DadosFornecedor.value = props.Dados
        element.imagen = 'clientes/image/'+props.Dados.imagem
        await axios.get(`/BuscarPretendFornecedor/${DadosFornecedor.value.id}`)
        .then((Response) => {
            PretendFornecedor.value = Response.data
            PretendFornecedor.value.ProdutosLista = Response.data.ProdutosLista.products
            PretedFornecedor.value = null
        }).catch((err) => {
            console.log(err);
        });
    }
})

const Desarquivar = (()=>{
    DadosFornecedor.value.estado = 1;
})

const GuardarFornecedor = (()=>{
    axios.post('/InserirCliente',{dados:DadosFornecedor.value})
    .then((Response) => {
        store.state.GuardarCliente = false
        toaste.add({
            severity: Response.data.tipo,
            summary: 'menssagem',
            detail: Response.data.message,
            life: 5000
        })
    }).catch((err) => {
        console.log(err);
    });
})

const GuardarFornecedore = (()=>{
    axios.post('/InserirFornecedor',{dados:DadosFornecedor.value})
    .then((Response) => {
        store.state.GuardarCliente = false
        toaste.add({
            severity: Response.data.tipo,
            summary: 'menssagem',
            detail: Response.data.message,
            life: 5000
        })

    }).catch((err) => {
        console.log(err);
    });
})

const Confirme = ((event , outro)=>{
    if (outro == 'confirmou') {
        axios.post('/UpdatedCliente',{id: DadosFornecedor.value.id,tipo: event})
        .then((Response) => {
            confirm.value = false
            toaste.add({
                severity: Response.data.tipo,
                summary: 'menssagem',
                detail: Response.data.message,
                life: 5000
            })
            if (Response.data.tipo =='success') {
                setTimeout(()=>{
                emits('voltar')
            },5000)
            }
        }).catch((err) => {
            console.log(err);
        });
    } else {
        confirm.value = false
    }
})

const DeletarImagem = (()=>{
    element.imagen = 'clientes/image/registro-sem-imagen.png'
    DadosFornecedor.value.imagem = 'registro-sem-imagen.png'
})

const guardar = computed(()=>{
    return store.state.GuardarCliente
})

watch(guardar,(novo)=>{
    GuardarFornecedore()
})

//===========================================
// Preview da imagem do cliente
//============================================

const onFileChange = (e) => {
  var files = e.target.files || e.dataTransfer.files;
  // Verificar o formato da imagem
  let arquivo = files[0].name;
  let extension = arquivo.indexOf(".") < 1 ? "" : arquivo.split(".").pop();
  if (extension == "") {
    return false;
  } else {
    // verificar se este formato existe no Array
    let formatos_permitidos = [
      "jpg",
      "png",
      "gif",
      "jpeg",
      "JPG",
      "PNG",
      "GIF",
      "JPEG",
    ];
    let resultado = formatos_permitidos.includes(extension);
    if (resultado == false) {
      return false;
    } else {
      // Tamanho da foto em MB
      var tamanho_maximo = 2242880;
      var fsizet = 0;
      for (var i = 0; i <= e.target.files.length - 1; i++) {
        // tamanho do arquivo
        var fsize = e.target.files.item(i).size;
        // total calculado
        fsizet = fsizet + fsize;
      }
      if (fsizet > tamanho_maximo) {
        console.log("tamanho de imagem muito grande");
      } else {
        createImg(files[0]);
      }
    }
  }
};

const createImg = (file) => {
  var imagem = new Image();
  var reader = new FileReader();

  reader.onload = (e) => {
    element.imagen = e.target.result;
    DadosFornecedor.value.imagem = element.imagen
  };
  reader.readAsDataURL(file);
};
</script>

<style scoped lang="scss">
@import "../../../assets/Clientes/css/NovoCliente";
</style>

<template>
  <Progress v-if="$store.state.StateProgress" />
  <Transition name="bounce">
    <NewCategory
      v-if="Categorias.StateModal"
      @FecharNewCateg="OnMounted"
      :categoria="Categorias.SingleCateg"
    />
  </Transition>
  <Muvementos
    :product="produto.produtos"
    v-if="$store.state.EstadoMuvemento.estado"
  />
  <div v-else class="Principal">
    <div class="titulo">
      <div class="titulo-left">
        <strong>Novo produto</strong>
        <div class="guardar_descartar">
          <button
            @click="submit"
            v-if="StateEditProd && produto.produtos.estado != 1"
            class=""
          >
            Guardar
          </button>
          <div
            v-if="!StateEditProd && produto.produtos.estado != 1"
            @click="StateEditProd = true"
          >
            Editar
          </div>
          <div
            v-if="StateEditProd && produto.produtos.estado != 1"
            @click="StateEditProd = false"
          >
            Descartar
          </div>
          <div @click="$emit('descartar')" class="mx-1 nao_guadar_artigo">
            Fechar
          </div>
        </div>
      </div>
      <Confirm
        :product="props.produto"
        @Voltar="$emit('descartar')"
        @produto="OnMounted"
        class="Acao"
      />
    </div>
    <div class="Formulario">
      <form class="FormNewProd">
        <div class="guard_descart">
          <div class="p-1 border-bottom">
            <div class="guardar_descartar" v-if="produto.produtos.estado != 1">
              <div @click="EntradaSaidaProd('Entrada')">Entrada do stock</div>
              <div @click="EntradaSaidaProd('Saida')">Saida do stock</div>
              <div>Detalhar</div>
            </div>
          </div>
        </div>
        <EntradaSaida
          :dados="Entradasaida"
          v-if="Entradasaida.estado"
          @Confirmar="OnMounted"
          @fechar="Entradasaida.estado = false"
        />
        <div class="form_novo_prod">
          <div class="h-100">
            <div class="TopProd">
              <div class="ProdutoBarraCima">
                <div class="d-flex">
                  <div class="BotaoMuvementos">
                    <div @click="muvementos('Entrada')" class="view_muvementos">
                      <i class="fa fa-cart-plus"></i>
                      <div class="w-100">
                        <div>{{ Entrada + ",00Un(s)" }}</div>
                        <strong class="TipoMuv">Comprados</strong>
                      </div>
                    </div>
                  </div>
                  <div class="BotaoMuvementos">
                    <div @click="muvementos('Saida')" class="view_muvementos">
                      <i class="fa fa-shopping-basket"></i>
                      <div class="w-100">
                        <div>{{ Saida + ",00Un(s)" }}</div>
                        <strong class="TipoMuv">Vendido</strong>
                      </div>
                    </div>
                  </div>
                  <div class="BotaoMuvementos">
                    <div
                      @click="muvementos('Muvementos')"
                      class="view_muvementos"
                    >
                      <i class="fa fa-exchange"></i>
                      <div class="w-100">
                        <div>{{ Muvemento + ",00Un(s)" }}</div>
                        <strong class="TipoMuv">Muvementos</strong>
                      </div>
                    </div>
                  </div>
                  <div class="BotaoMuvementos">
                    <div class="view_muvementos">
                      <i class="fa fa-bar-chart"></i>
                      <div class="w-100">
                        <div>{{ produto.produtos.qtd + ",00Un(s)" }}</div>
                        <strong class="TipoMuv">Stock real</strong>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="FormContainer">
                <div class="input_nome_tipo">
                  <div>
                    <div>
                      <label for="nome_do_artigo">Nome do Artigo</label>
                    </div>
                    <div>
                      <input
                        type="text"
                        v-model="produto.produtos.nome"
                        class="nome_artigo_data"
                        :disabled="
                          produto.produtos.estado === 1 || !StateEditProd
                        "
                        id="nome_do_artigo"
                        placeholder="Digite nome de produto"
                      />
                    </div>
                  </div>
                  <div class="ContainerInfo">
                    <div class="Titulos">
                      <div class="w-100 text-sm p-1">
                        <strong>Tipo de artigo</strong>
                      </div>
                      <div class="w-100 text-sm p-1">
                        <strong>QR codigo de barra</strong>
                      </div>
                    </div>

                    <div class="InfoContainer">
                      <div class="TipoCategoria">
                        <div class="tipoartigo">
                          <select
                            :disabled="
                              produto.produtos.estado === 1 || !StateEditProd
                            "
                            class="tipo_artigo"
                          >
                            <option selected>
                              {{ produto.produtos.tipoartigo }}
                            </option>
                            <option>Artigo armagenavel</option>
                            <option>Serviço</option>
                          </select>
                        </div>

                        <div class="DivCategoria">
                          <select
                            @change="AddCategoryObject"
                            :disabled="
                              produto.produtos.estado === 1 || !StateEditProd
                            "
                            class="Categoria"
                          >
                            <option v-if="produto.produtos.categoria == null">
                              Selecionar Categoria
                            </option>
                            <option
                              :selected="
                                produto.produtos.categoria == categoria.id
                              "
                              v-for="categoria in Categorias.lista"
                              :key="categoria.id"
                              :value="categoria.Nome"
                            >
                              {{ categoria.Nome }}
                            </option>
                          </select>
                          <button
                            class="AdicionarCategoria"
                            type="button"
                            @click.prevent="NewCategoria(produto.produtos.id)"
                          >
                            <i class="fa fa-plus"></i>Add Categoria
                          </button>
                        </div>
                      </div>
                      <div class="CodeBar">
                        <input
                          type="text"
                          placeholder="QR code bar"
                          :disabled="
                            produto.produtos.estado === 1 || !StateEditProd
                          "
                          v-model="produto.produtos.codego"
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="DivImagem">
                  <div class="HeaderImagem">
                    <div v-if="produto.produtos.estado === 1" class="Arquivado">
                      <h2>ARQUIVADO</h2>
                    </div>
                    <div class="d-flex icones" v-else>
                      <label for="imagem">
                        <input
                          type="file"
                          @change="onFileChange"
                          ref="fileInput"
                          id="imagem"
                          class="position-absolute d-none"
                        />
                        <i
                          class="fa fa-pencil-square-o EditarImagen"
                          aria-hidden="true"
                        ></i>
                      </label>
                      <div @click="RemoveImage" id="icone_delete_img">
                        <i class="fa fa-ban" aria-hidden="true"></i>
                      </div>
                    </div>
                  </div>

                  <label class="imagem_novo_produto">
                    <img :src="element.img" class="rounded float-right" />
                  </label>
                </div>
              </div>
            </div>
            <EnfoProd @Precos="Precos" :produto="produto" />
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/inertia";
import { onMounted, reactive, ref } from "@vue/runtime-core";
import { useStore } from "vuex";
import EntradaSaida from "./EntradaSaida.vue";
import Muvementos from "./muvementos.vue";
import EnfoProd from "./EnfoProd.vue";
import Progress from "@/components/confirmation/progress.vue";
import Confirm from "../confirmation/confirm.vue";
import NewCategory from "@/components/produtos/NewCategori.vue";
const emits = defineEmits(["descartar", "Guardar"]);
const store = useStore();
const StateEditProd = ref(false);
const props = defineProps({
  produto: Object,
});

const Categorias = reactive({
  lista: [],
  SingleCateg: {
    idProd: null,
    categoria: null,
  },
  StateModal: false,
});

const element = reactive({
  img: "/produtos/image/" + props.produto.imagem,
});

const Entrada = ref(0);
const Saida = ref(0);
const Muvemento = ref(0);
const ListePrecoQtd = ref();
const ListePreco = ref();
const image = ref();
const FormetDineiro = new Intl.NumberFormat("pt-AO", {
  style: "currency",
  currency: "AOA",
});

const produto = reactive({
  produtos: props.produto,
  imagem: null,
  tipo: "precos",
});

const EntradaSaidaProd = (event) => {
  Entradasaida.value.tipo = event;
  Entradasaida.value.product = produto.produtos;
  Entradasaida.value.estado = true;
};

const muvementos = (event) => {
  store.state.EstadoMuvemento.estado = true;
  store.state.EstadoMuvemento.tipo = event;
};

const NewCategoria = (idProd) => {
  Categorias.StateModal = true;
  Categorias.SingleCateg.idProd = idProd;
};

const AddCategoryObject = (event) => {
  produto.produtos.categoria = event;
  let CategoriaFiltrada = Categorias.lista.filter(
    (item) => item.Nome === event.target.value
  );
  produto.produtos.categoria = CategoriaFiltrada[0].id;
};

const OnMounted = onMounted(async () => {
  store.state.StateProgress = true;
  await axios
    .get("/novo_produto?id_prod=" + props.produto.id)
    .then((Response) => {
      Entrada.value = Response.data.Entrada;
      Saida.value = Response.data.Saida;
      Muvemento.value = Response.data.Muvemento;
      produto.produtos = Response.data.produto;
      Categorias.lista = Response.data.categorias;
      store.state.StateProgress = false;
      (Categorias.StateModal = false), (StateEditProd.value = true);
    })
    .catch((err) => {
      emits("descartar");
      console.log(err);
      store.state.StateProgress = false;
    });
});

const Precos = (tipo, valor) => {
  produto.produtos[tipo] = valor;
};

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
    element.img = e.target.result;
    produto.imagem = element.img;
  };

  reader.readAsDataURL(file);
};

const RemoveImage = () => {
  element.img = "/produtos/image/produto-sem-imagen.png";
  produto.produtos.imagem = "produto-sem-imagen.png";
};

const submit = () => {
  store.state.StateProgress = true;
  if (produto.imagem != null) {
    produto.imagem = element.img;
  }
  axios
    .post("/inserir_informacao_prod", produto)
    .then((Response) => {
      store.state.StateProgress = false;
      StateEditProd.value = false;
      emits("Guardar", Response.data);
    })
    .catch((err) => {
      store.state.StateProgress = false;
      console.log(err);
    });
};

const Entradasaida = ref({
  estado: null,
  tipo: null,
  product: produto.produtos,
});
</script>

<style lang="scss" scoped>
@import "../../../assets/produtos/css/produto";
</style>

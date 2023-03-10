<script setup>
const onFileChange = (e) => {
    var files = e.target.files || e.dataTransfer.files;
    // Verificar o formato da imagem
    let arquivo = files[0].name;
    let extension = arquivo.indexOf('.') < 1 ? '' : arquivo.split('.').pop();
    if (extension == "") {
        return false;
    } else {
        // verificar se este formato existe no Array
        let formatos_permitidos = ['jpg', 'png', 'gif', 'jpeg', 'JPG', 'PNG', 'GIF', 'JPEG'];
        let resultado = formatos_permitidos.includes(extension);
        if (resultado == false) {
            return false;
        } else {
            // Tamanho da foto em MB
            var tamanho_maximo = 2242880
            var fsizet = 0;
            for (var i = 0; i <= e.target.files.length - 1; i++) {
            // tamanho do arquivo
            var fsize = e.target.files.item(i).size;
            // total calculado
            fsizet = fsizet + fsize;
            }
            if (fsizet > tamanho_maximo) {
                console.log('tamanho de imagem muito grande');
            } else {
                createImg(files[0]);
            }
        }
    }
}

const createImg = (file) => {
    var imagem = new Image;
    var reader = new FileReader;

    reader.onload = (e) => {
    element.img = e.target.result;
    produto.imagem = element.img
    };

    reader.readAsDataURL(file);
}

const RemoveImage = () => {
    element.img = '/produtos/image/produto-sem-imagen.png';
    produto.produtos.imagem = 'produto-sem-imagen.png'
}
</script>

<style>

</style>

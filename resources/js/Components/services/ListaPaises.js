export default class Pais {

    BuscarPaises() {
        return fetch('data/paises.json').then(res => res.json())
            .then(d => d.data);
    }
}
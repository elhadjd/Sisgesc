import { Inertia } from "@inertiajs/inertia";
import {watch} from 'vue';
import useEventsBus from '@/Eventbus';

const {emit,bus} = useEventsBus();
let estado = false

watch(()=>bus.value.get('Logado'), (payload) => {
   estado = false
});

function inactivityTime() {
    let time;
    // redefinir cronômetro
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeydown = resetTimer;

    function doSomething() {
        if (estado == false) {
            emit('EncerrarSessao');
            estado = true
        } else {
            return false
        }
    }

    function resetTimer() {
        // 3 Minutos para encerrar a sessão
        const minutos = 180000;
        clearTimeout(time);
        time = setTimeout(doSomething, minutos)
    }

}

inactivityTime();

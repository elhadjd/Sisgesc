import './bootstrap';
import 'bootstrap/dist/js/bootstrap.min.js'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'primevue/resources/themes/saga-blue/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'
import '../css/app.css';
import '../assets/deshboard/css/deshboard.scss'
import '../assets/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css'
import '../css/variables.scss';
import '../css/transicao.css';
import useEventsBus from './Eventbus';


import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import ConfirmationService from 'primevue/confirmationservice';
import  primevue  from 'primevue/config';
import ToastService from 'primevue/toastservice';
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import store from './store'
import { faProductHunt, faTwitter } from '@fortawesome/free-brands-svg-icons';
import { faArrowDown, faArrowsSplitUpAndLeft, faArrowUpRightFromSquare, faCartShopping, faChartGantt, faChartLine, faDownLong, faEye, faFloppyDisk, faHandHoldingDollar, faMoneyBill1Wave, faMoneyCheckDollar, faOutdent, faPenToSquare, faPlus, faReceipt, faRightLong, faRotateRight, faSackDollar, faStar, faTags, faTrash, faUpLong, faUsers, faXmark } from '@fortawesome/free-solid-svg-icons';
library.add(faTwitter,faUpLong,faRotateRight,faRightLong,faChartLine,faDownLong,faChartGantt,faArrowsSplitUpAndLeft,faArrowUpRightFromSquare,faStar,faCartShopping,faProductHunt,faMoneyCheckDollar,faPlus,faUsers,faPenToSquare,faEye,faSackDollar,faMoneyBill1Wave,faHandHoldingDollar,faTrash,faFloppyDisk,faOutdent,faTags,
faArrowDown,faReceipt,faXmark)

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
        .use(plugin)
        .use('font-awesome-icon',FontAwesomeIcon)
        .use(useEventsBus)
        .use(ZiggyVue, Ziggy)
        .use(primevue)
        .use(ConfirmationService)
        .use(ToastService)
        .use(store)
        .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

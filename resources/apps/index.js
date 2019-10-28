import Vue from 'vue';
import Axios from 'axios';

Vue.config.productionTip = false;

import Apps from './pages/Apps.vue';
import vuetify from './plugins/vuetify';

Object.defineProperty(Vue.prototype, '$http', {
    get() {
        let headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': (document.head.querySelector('meta[name="csrf-token"]')).content
        };

        return Axios.create({ headers });
    }
});

new Vue({
    vuetify,
    render: h => h(Apps)
}).$mount('#monoland');
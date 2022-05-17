window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    require('bootstrap/dist/js/bootstrap.bundle.min.js');
    window.moment = require('moment');
    require('daterangepicker/daterangepicker.js');
    require('@fortawesome/fontawesome-free');
    require('overlayscrollbars');
    require('admin-lte');
    require('admin-lte/dist/js/demo.js');
    require('datatables.net');
    require('datatables.net-bs4');
    window.toastr=require('toastr');
    window.Swal = require('sweetalert2');
    window.select2=require('select2');
    window.FileSaver=require('file-saver');
    window.n2words = require('n2words');
    window.printThis = require('printThis/printThis.js');
    window.xlsx=require('json-as-xlsx/index.js');
} catch (e) {}
 

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// window.axios.defaults.baseURL = 'http://localhost/saoda_accounts/public';
window.axios.defaults.baseURL = 'www.saudaenterprise.com/accounts';
// window.axios.defaults.baseURL = (process.env.NODE_ENV !== 'production') ? 'http://localhost/accounts3/public' : ''

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

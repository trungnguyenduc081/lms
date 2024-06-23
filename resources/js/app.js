import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
import jQuery from "jquery";
import select2 from "select2";
import "/node_modules/select2/dist/css/select2.css";
import flatpickr from "flatpickr";
import 'flatpickr/dist/flatpickr.css';
window.flatpickr = flatpickr;
window.$ = jQuery;
select2();
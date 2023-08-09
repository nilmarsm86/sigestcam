import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import './bootstrap/js/bootstrap.min.js';
import './js/bs-init.js';
import './js/theme.js';
import Backdrop from "./components/backdrop.js";

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉')
customElements.define("backdrop-component", Backdrop);//register un webcomponent

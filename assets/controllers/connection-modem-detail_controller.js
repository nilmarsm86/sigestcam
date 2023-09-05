import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {

    initialize() {
        window.addEventListener('App\\Components\\Live\\ConnectionCamera\\ConnectionModemDetail_activate_Simple', (event) => {
            event.preventDefault();
            alert('Para poder activar un modem debe estar conectado a un puerto o modem.');
            this.element.querySelector('#' + event.detail.elementId).checked = false;
        });
    }


}

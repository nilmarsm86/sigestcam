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
        window.addEventListener('App\\Components\\Live\\ConnectionModem\\ConnectionModemDetail_activate_Simple',this.notConnectedModem.bind(this));
        window.addEventListener('App\\Components\\Live\\ConnectionModem\\ConnectionModemDetail_activate_SlaveModem',this.notConnectedModem.bind(this));
    }

    notConnectedModem(event){
        event.preventDefault();

        if(this.element.querySelector('#' + event.detail.elementId)){
            alert('Para poder activar un modem debe estar conectado a un puerto o modem.');
            this.element.querySelector('#' + event.detail.elementId).checked = false;
        }
    }


}

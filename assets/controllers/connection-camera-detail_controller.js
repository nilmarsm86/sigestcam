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
        window.addEventListener('App\\Components\\Live\\ConnectionCamera\\ConnectionCameraDetail_activate_Direct', (event) => {
            event.preventDefault();
            alert('Para poder activar una camara debe estar conectada a un puerto o modem.');
            this.element.querySelector('#' + event.detail.elementId).checked = false;

        });
    }

    notAssociateCamera(event){
        event.preventDefault();

        event.currentTarget.checked = false;
        alert('Ya el modem tiene conectado 4 camaras, no se puede asociar esta cámara.');
    }


}

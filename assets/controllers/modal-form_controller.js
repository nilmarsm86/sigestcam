import { Controller } from '@hotwired/stimulus';
import '../bootstrap/js/bootstrap.min.js';

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
    modal = null;

    initialize() {
        this.modal = bootstrap.Modal.getOrCreateInstance(this.element);
        window.addEventListener('modal_form_close', () => {
            // this.modal.hide();
            // this.modal.dispose();
            this.element.querySelector('button[class=btn-close]').click();
        });
    }
}
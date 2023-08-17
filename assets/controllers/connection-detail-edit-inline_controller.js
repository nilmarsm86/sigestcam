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

    initialize() {
        const button = this.element.querySelector('.secure_password');
        let popover = new bootstrap.Popover(button, {
            'title': 'Contraseña',
            'content': '******'
        });

        window.addEventListener('App\\Components\\Live\\ConnectionDetailEditInline_show_secure', (event) => {
            event.preventDefault();

            popover = bootstrap.Popover.getOrCreateInstance('.secure_password');
            popover.setContent({
                '.popover-header': 'Contraseña',
                '.popover-body': event.detail.data
            });
        });
    }


}

import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

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
    static values = {
        msam: Number,
    }

    async initialize() {
        this.component = await getComponent(this.element);
    }

    deactivate(event) {
        event.preventDefault();
        if(!confirm('Está seguro que desea desactivar este msam, esta acción desactivará tambien los equipos conectados a él.')){
            event.currentTarget.checked = true;
            return ;
        }

        // or call an action
        this.component.action('deactivate', { entityId: this.msamValue });
        //this.component.render();
    }
}

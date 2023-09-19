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

    async initialize() {
        this.component = await getComponent(this.element);
        this.element.addEventListener('page:onChangeState', this.state.bind(this));
    }

    state(event) {
        event.preventDefault();

        this.component.action('status');
    }

    disconnect(event){
        event.preventDefault();

        if(!confirm("Está seguro que desea eliminar la conexión?")){
            return ;
        }
        this.component.action('disconnect', {'camera': event.params.camera});
    }
}

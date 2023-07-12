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
    static values = {
        queryName: String,
    }

    connect() {
        this.element.addEventListener('change', this.onChange.bind(this));
    }

    /**
     * Cambiar la cantidad de elementos a mostrar por pagina
     * @param event change select event
     */
    onChange(event){
        this.dispatch('onChange');
        let currentPath = new URL(document.location);
        currentPath.searchParams.set(this.queryNameValue, event.currentTarget.value);
        document.location = currentPath.toString();
    }

    /**
     * @inheritDoc
     */
    dispatch(eventName, options = {}) {
        const event = super.dispatch(eventName, options);
        console.groupCollapsed(`Trigger ${event.type}`);
        console.log(event.detail);
        console.groupEnd();
        return event;
    }
}

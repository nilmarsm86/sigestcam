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
    static targets = ['formContainer', 'listContainer'];

    /**
     * When form send success
     * @param event
     */
    sendFormSuccess(event){
        const form = event.detail.form;
        form.reset();

        super.dispatch('onSendFormSuccess',{detail:{container:this.listContainerTarget, url: document.location}});
    }

    /**
     * When add form show
     * @param event
     */
    showFormContent(event){
        event.preventDefault();

        super.dispatch('onShowFormContent',{detail:{container:this.formContainerTarget, url: event.currentTarget.href}});
    }
}

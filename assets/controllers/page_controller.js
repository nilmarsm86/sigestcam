import { Controller } from '@hotwired/stimulus';
import {useProcessResponse} from "../behaviors/use-process-response.js";

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

    connect() {
        useProcessResponse(this);
    }

    /**
     * When form send success
     * @param event
     */
    async sendFormSuccess(event){
        const form = event.detail.form;
        form.reset();

        const response = event.detail.response;
        await this.processResponseToast(response);

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

    // async processResponse(response){
    //     const responseText = await response.text();
    //     const nodes = new DOMParser().parseFromString(responseText, 'text/html').body.childNodes;
    //     let id = nodes[0].id;
    //     document.querySelector('.toast-container').appendChild(nodes[0]);
    //
    //     const toastBootstrap = bootstrap.Toast.getOrCreateInstance(document.querySelector(`#${id}`));
    //     toastBootstrap.show();
    // }

    async state(event){
        //event.preventDefault();

        const request = new Request(event.currentTarget.href, {
            headers: new Headers({'X-Requested-With': 'XMLHttpRequest'}),
        });

        let data = new FormData();
        data.set('id', event.params.id);
        data.set('state', event.params.state);

        let response = await fetch(request, {
            method: 'POST',
            body: new URLSearchParams(data),
        });
        await this.processResponseToast(response);

        super.dispatch('onChangeState',{detail:{container:this.listContainerTarget, url: document.location}});
    }

    // place(event){
    //     event.preventDefault();
    //
    //     //const place = event.currentTarget.innerText.trim().replace(/ /g, '+');
    //     const url = new URL(document.location);
    //     url.searchParams.set('filter', event.currentTarget.innerText);
    //
    //     document.location = url.toString();
    // }


}

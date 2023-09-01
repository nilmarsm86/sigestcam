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

    static values = {
        stateHref: String,
        speedHref: String,
        listHref: String,
        typeHref: String,
    }

    connect() {
        useProcessResponse(this);
    }

    async state(event){
        this.change(event, this.stateHrefValue, 'state', event.params.state);
    }

    async speed(event){
        event.preventDefault();

        this.change(event, this.speedHrefValue, 'speed', event.currentTarget.value);
    }

    type(event){
        this.change(event, this.typeHrefValue, 'type', event.currentTarget.value);
    }

    async change(event, href, dataName, dataValue){
        const request = new Request(href, {
            headers: new Headers({'X-Requested-With': 'XMLHttpRequest'}),
        });

        let data = new FormData();
        data.set('id', event.params.id);
        data.set(dataName, dataValue);

        let response = await fetch(request, {
            method: 'POST',
            body: new URLSearchParams(data),
        });
        await this.processResponseToast(response);

        super.dispatch('onChangeState',{detail:{url: this.listHrefValue}});
    }


}

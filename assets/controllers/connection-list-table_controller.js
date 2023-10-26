import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';
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

    connect() {
        useProcessResponse(this);
    }

    async initialize() {
        try{
            this.component = await getComponent(this.element);
        }catch (e){

        }

        this.element.addEventListener('page:onChangeState', this.state.bind(this));

        const modalElement = this.element.querySelector('#new-report');
        modalElement.addEventListener('hidden.bs.modal', event => {
            modalElement.querySelectorAll('.alert-danger').forEach((alert) => {
                alert.remove();
            });
        });
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

    report(event){
        event.preventDefault();

        const modalElement = this.element.querySelector('#new-report');
        modalElement.querySelector('#report_equipment').value = event.params.equipment;
        modalElement.querySelector('#report_type').value = event.params.type;

        const myModal = new bootstrap.Modal(modalElement);
        myModal.show();
    }

    async closeModal(event){
        const modalElement = this.element.querySelector('#new-report');
        const form = modalElement.querySelector('form');
        form.reset();

        modalElement.querySelector('button[class=btn-close]').click();

        const response = event.detail.response;
        await this.processResponseToast(response);
    }

}

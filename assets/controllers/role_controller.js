import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        user: Number,
        role: Number,
        url: String
    }

    async onChange(event){
        //event.preventDefault();
        this.dispatch('startChange');

        if(event.target.checked){
            const url = new URL(this.urlValue, document.location.origin);
            url.searchParams.set('fetch', 1);
            const request = new Request(url.toString(), {
                headers: new Headers({'X-Requested-With': 'XMLHttpRequest'}),
            });

            let data = new FormData();
            data.set('user', this.userValue);
            data.set('role', this.roleValue);

            const response = await fetch(request, {
                method: 'POST',
                body: new URLSearchParams(data),
            });

            if(response.ok){
                console.log('Rol agregado');
                //poner un toast
                this.dispatch('endChange');
                //event.target.checked = false;
            }
        }else{
            //eliminar el rol
            console.log('eliminar el rol');
        }

        //event.resume();
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

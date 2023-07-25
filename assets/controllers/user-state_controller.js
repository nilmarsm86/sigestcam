import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        user: Number,
        url: String
    }

    async onChange(event){
        if(!event.target.checked && !confirm('Está seguro que desea desactivar el usuario?')){
            event.target.checked = true;
            return ;
        }

        this.dispatch('startChange');
        let response = null;
        if(event.target.checked){
            response = await this.doRequest('activate');
        }else{
            response = await this.doRequest('dectivate');
            if(!response.ok){
                event.target.checked = true;
            }

            if(response.status === 422){
                event.target.disabled = true;
            }
        }
        await this.processResponse(response);
        this.dispatch('endChange');
    }

    async processResponse(response){
        const responseText = await response.text();
        const nodes = new DOMParser().parseFromString(responseText, 'text/html').body.childNodes;
        let id = nodes[0].id;
        document.querySelector('.toast-container').appendChild(nodes[0]);

        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(document.querySelector(`#${id}`));
        toastBootstrap.show();
    }

    async doRequest(action){
        const url = new URL(this.urlValue, document.location.origin);
        url.searchParams.set('fetch', '1');
        const request = new Request(url.toString(), {
            headers: new Headers({'X-Requested-With': 'XMLHttpRequest'}),
        });

        let data = new FormData();
        data.set('user', this.userValue);
        data.set('action', action);

        return await fetch(request, {
            method: 'POST',
            body: new URLSearchParams(data),
        });
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

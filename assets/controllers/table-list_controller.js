import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['showSelect']
    static values = {
        url: String,
    }

    currentPath = '';

    connect(){
        this.currentPath = new URL(document.location);
    }

    /**
     * Cambiar la cantidad de elementos a mostrar por pagina
     * @param event change select event
     */
    amountToDisplayPerPage(event){
        /*if(this.currentPath.searchParams.has('amount')){
            if(this.currentPath.searchParams.get('amount') === event.target.value){
                return ;
            }
        }*/
        this.currentPath.searchParams.set('amount', event.target.value);
        document.location = this.currentPath.toString();
    }
    // ...
}

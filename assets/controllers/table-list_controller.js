import { Controller } from '@hotwired/stimulus';
import Backdrop from "../components/backdrop.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['backdropComponent']

    /**
     *
     */
    connect(){
        customElements.define("backdrop-component", Backdrop);//register un webcomponent

        /*this.element.addEventListener(`table-list:${BACKDROP_SHOW_EVENT}`, (event) => {
            this.backdropComponentTarget.dispatchEvent(new CustomEvent(BACKDROP_SHOW_EVENT, {
                detail: {}
            }));
            //this.backdropComponentTarget.onShow();
        });*/

        /*this.element.addEventListener(`table-list:${BACKDROP_HIDE_EVENT}`, (event) => {
            this.backdropComponentTarget.dispatchEvent(new CustomEvent(BACKDROP_HIDE_EVENT, {
                detail: {}
            }));
            //this.backdropComponentTarget.onHide();
        });*/

        /*this.selectAmountTarget.addEventListener('select-navigate-querystring:onPreChange', (event) => {
            this.backdropComponentTarget.onShow();
        });*/
    }

    /**
     * Show backdrop
     * @param event
     */
    showBackdrop(event){
        this.backdropComponentTarget.onShow();
    }

    /**
     * Hide backdrop
     * @param event
     */
    hideBackdrop(event){
        this.backdropComponentTarget.onHide();
    }

}

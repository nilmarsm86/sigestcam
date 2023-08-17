import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['province', 'municipality'];
    static values = {
        url: {type: String, default: ''},
    };

    /**
     * When add form show
     * @param event
     */
    selectProvince(event){
        super.dispatch('onSelectProvince', {
            detail:{
                container: this.municipalityTarget,
                url: this.urlValue.replace('0', this.provinceTarget.value)
            }
        });
        this.municipalityTarget.disabled = false;
    }

}

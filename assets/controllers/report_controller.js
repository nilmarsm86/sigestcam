import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {

    initialize() {
        let flawField = this.element.querySelector('#report_flaw');
        let detailField = this.element.querySelector('#report_detail');
        for(let option in flawField.options){
            if(detailField.value === flawField.options[option].text){
                flawField.selectedIndex = option;
            }
        }

        if(detailField.value && flawField.selectedIndex === 0){
            flawField.selectedIndex = -1;
        }
    }

    selectReason(event){
        event.preventDefault();

        let interruptionReasonField = this.element.querySelector('#report_interruptionReason');
        let interruptionReasonRow = interruptionReasonField.parentElement.parentElement;
        if(event.currentTarget.value === "-1"){
            interruptionReasonRow.style.display = 'flex';

            // interruptionReasonField.value = '';
        }else{
            interruptionReasonRow.style.display = 'none';
            interruptionReasonField.value = event.currentTarget.options[event.currentTarget.selectedIndex].text;
        }
    }

    selectFlaw(event){
        event.preventDefault();

        let detailField = this.element.querySelector('#report_detail');
        // let flawField = this.element.querySelector('#report_flaw');
        let detailRow = detailField.parentElement.parentElement;
        if(event.currentTarget.value === "-1"){
            detailRow.style.display = 'flex';
            detailField.value = '';
        }else{
            detailRow.style.display = 'none';
            detailField.value = event.currentTarget.options[event.currentTarget.selectedIndex].text;
        }
    }

    closeReport(event){
        event.preventDefault();

        let solutionPrompt = window.prompt('Solución dada:')
        if(solutionPrompt === null){
            window.alert('Para poder cerrar un reporte se le debe haber dado una solución.');
        }else{
            let solutionField = this.element.querySelector('#report_solution');
            solutionField.value = solutionPrompt;

            this.element.submit();
        }
    }

}

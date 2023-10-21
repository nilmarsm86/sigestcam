import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {

    initialize() {
        let interruptionReasonField = this.element.querySelector('#report_interruptionReason');
        let interruptionField = this.element.querySelector('#report_interruption');
        for(let option in interruptionField.options){
            if(interruptionReasonField.value === interruptionField.options[option].text){
                interruptionField.selectedIndex = option;
            }
        }

        if(interruptionReasonField.value && interruptionField.selectedIndex === 0){
            interruptionField.selectedIndex = 7;
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

export const useProcessResponse = (controller, options) => {
    Object.assign(controller, {

        async processResponseToast(response){
            const responseText = await response.text();
            const nodes = new DOMParser().parseFromString(responseText, 'text/html').body.childNodes;
            let id = nodes[0].id;
            document.querySelector('.toast-container').appendChild(nodes[0]);

            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(document.querySelector(`#${id}`));
            toastBootstrap.show();
        }

    });
};
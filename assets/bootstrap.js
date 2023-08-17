import { startStimulusApp } from '@symfony/stimulus-bundle';
import {ContentLoaderController} from "./behaviors/content-loader/content-loader_controller.js";//importar lo que exporta el index.js
import {VisibilityController} from "./behaviors/visibility/visibility_controller.js";//importar lo que exporta el index.js
import {SendAsyncFormController} from "./behaviors/submit-form-async/submit-form-async_controller.js";

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
app.register('content-loader', ContentLoaderController);
app.register('visibility', VisibilityController);
app.register('submit-form-async', SendAsyncFormController);
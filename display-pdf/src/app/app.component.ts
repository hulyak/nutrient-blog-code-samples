import { Component } from '@angular/core';
import NutrientViewer from '@nutrient-sdk/viewer';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css'],
    standalone: true,
})
export class AppComponent {
    title = 'Nutrient Web SDK Angular Example';

    ngAfterViewInit() {
        NutrientViewer.load({
            // Use the assets directory URL as a base URL. Nutrient will download its library assets from here.
            baseUrl:
                location.protocol + '//' + location.host + '/assets/',
            document: '/example.pdf',
            container: '#pspdfkit-container',
        }).then((instance) => {
            // Store the Nutrient instance on the global object
            // so you can interact with the API in the dev tools.
            (window as any).instance = instance;
        });
    }
}

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
    private nutrientInstance: any;

    ngAfterViewInit(): void {
        NutrientViewer.load({
            // Use the assets directory URL as a base URL. Nutrient will download its library assets from here.
            baseUrl: location.protocol + '//' + location.host + '/assets/',
            document: '/assets/example.pdf',
            container: '.nutrient-container',
            // licenseKey: 'YOUR_LICENSE_KEY_GOES_HERE', // Optional license key.
        }).then((instance) => {
            // For the sake of this demo, store the Nutrient Web SDK instance
            // on the global object so that you can open the dev tools and
            // play with the Nutrient API.

            (<any>window).instance = instance;
            this.nutrientInstance = instance;

            // Create annotations
            this.createTextAnnotation();
            this.createInkAnnotation({
                x1: 5,
                y1: 5,
                x2: 100,
                y2: 100,
            });
        });
    }

    async createTextAnnotation() {
        const annotation = new NutrientViewer.Annotations.TextAnnotation({
            pageIndex: 0, // Specify the page number for the annotation.
            text: {
                format: 'plain',
                value: 'Welcome to Nutrient', // Text to embed.
            },
            font: 'Helvetica',
            isBold: true,
            horizontalAlign: 'left', // Align the annotation to the left of the bounding box.
            boundingBox: new NutrientViewer.Geometry.Rect({
                left: 50, // Position of the annotation.
                top: 200,
                width: 100,
                height: 80,
            }),
            fontColor: NutrientViewer.Color.BLUE, // Color of the text.
        });

        // Attach this annotation to your PDF.
        const createdAnnotation = await this.nutrientInstance.create(
            annotation,
        );
        console.log('Text annotation created:', createdAnnotation);
    }

    async createInkAnnotation({
        x1,
        y1,
        x2,
        y2,
    }: {
        x1: number;
        y1: number;
        x2: number;
        y2: number;
    }) {
        const { List } = NutrientViewer.Immutable;
        const { DrawingPoint, Rect } = NutrientViewer.Geometry;
        const { InkAnnotation } = NutrientViewer.Annotations;

        const annotation = new InkAnnotation({
            pageIndex: 0,
            boundingBox: new Rect({ width: 400, height: 100 }),
            strokeColor: new NutrientViewer.Color({ r: 255, g: 0, b: 255 }),
            lines: List([
                List([
                    new DrawingPoint({ x: x1, y: y1 }),
                    new DrawingPoint({ x: x2, y: y2 }),
                ]),
            ]),
        });

        const createdAnnotation = await this.nutrientInstance.create(annotation);
        console.log('Ink annotation created:', createdAnnotation);
    }
}

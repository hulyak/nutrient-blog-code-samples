<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        />
        <title>Nutrient Laravel PDF Viewer</title>
    </head>
    <body>
        <div id="nutrient" style="width: 100%; height: 100vh;"></div>

        <script type="module">
            import '/assets/nutrient-viewer.js';

            const container = document.getElementById('nutrient');
            const baseUrl = `${window.location.protocol}//${window.location.host}/assets/`;

            NutrientViewer.unload(container);

            NutrientViewer.load({
                container,
                document: 'document.pdf',
                baseUrl,
            })
                .then((instance) => {
                    console.log('Nutrient loaded', instance);
                })
                .catch((error) => {
                    console.error(error.message);
                });
        </script>
    </body>
</html>

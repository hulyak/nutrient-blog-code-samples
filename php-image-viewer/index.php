<!DOCTYPE html>
<html>
    <head>
        <title>My App</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    </head>
    <body>
        <div id="pspdfkit" style="height: 100vh;"></div>
        <script src="assets/nutrient-viewer.js"></script>
        <script>
            const baseUrl = `${window.location.protocol}//${window.location.host}/assets/`;

            NutrientViewer.load({
                baseUrl,
                container: '#pspdfkit',
                document: 'image.png',
            })
                .then(function (instance) {
                    console.log('Nutrient loaded', instance);
                })
                .catch(function (error) {
                    console.error(error.message);
                });
        </script>
    </body>
</html>

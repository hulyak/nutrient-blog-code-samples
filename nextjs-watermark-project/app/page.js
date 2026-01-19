'use client';
import { useEffect, useRef } from 'react';

export default function App() {
    const containerRef = useRef(null);

    useEffect(() => {
        const container = containerRef.current;

        if (typeof window !== 'undefined') {
            import('@nutrient-sdk/viewer').then((NutrientViewer) => {
                if (NutrientViewer) {
                    NutrientViewer.unload(container);
                }

                NutrientViewer.load({
                    container,
                    document: '/document.pdf',
                    baseUrl: `${window.location.protocol}//${window.location.host}/`,
                    renderPageCallback: function (ctx, pageIndex, pageSize) {
                        ctx.beginPath();
                        ctx.fillStyle = 'red';
                        ctx.fillRect(0, 0, pageSize.width, 40);
                        ctx.stroke();
                        ctx.font = '30px Comic Sans MS';
                        ctx.fillStyle = 'white';
                        ctx.textAlign = 'right';
                        ctx.fillText(
                            `This is page ${pageIndex + 1}`,
                            pageSize.width,
                            30,
                        );
                    },
                });
            });
        }
    }, []);

    return <div ref={containerRef} style={{ height: '100vh' }} />;
}

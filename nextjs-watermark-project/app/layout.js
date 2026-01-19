export const metadata = {
    title: 'Next.js Watermark Example',
    description: 'Add watermarks to PDFs with Nutrient Web SDK',
};

export default function RootLayout({ children }) {
    return (
        <html lang="en">
            <body style={{ margin: 0 }}>{children}</body>
        </html>
    );
}

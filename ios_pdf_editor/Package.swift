// swift-tools-version:5.9
import PackageDescription

let package = Package(
    name: "PDFEditorTest",
    platforms: [
        .iOS(.v15)
    ],
    products: [
        .library(
            name: "PDFEditorTest",
            targets: ["PDFEditorTest"]),
    ],
    dependencies: [
        .package(url: "https://github.com/nickmain/pspdfkit-sp", from: "10.0.0")
    ],
    targets: [
        .target(
            name: "PDFEditorTest",
            dependencies: [
                .product(name: "PSPDFKit", package: "pspdfkit-sp"),
                .product(name: "PSPDFKitUI", package: "pspdfkit-sp")
            ]),
    ]
)

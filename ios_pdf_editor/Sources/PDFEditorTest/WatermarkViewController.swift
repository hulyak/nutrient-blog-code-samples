import UIKit
import PSPDFKit
import PSPDFKitUI

class WatermarkViewController: UIViewController {

    override func viewDidAppear(_ animated: Bool) {
       super.viewDidAppear(animated)

       // Update to use your document name.
       let fileURL = Bundle.main.url(forResource: "document-ios", withExtension: "pdf")!
       let document = Document(url: fileURL)

       guard let configuration = Processor.Configuration(document: document) else {
           print("Could not create a processor configuration. The document might be locked or invalid.")
           return
       }

       configuration.drawOnAllCurrentPages { context, pageIndex, pageRect, renderOptions in
           // Careful. This code is executed on background threads. Only use thread-safe drawing methods.
           let text = "PSPDF Live Watermark On Page \(pageIndex + 1)"
           let stringDrawingContext = NSStringDrawingContext()
           stringDrawingContext.minimumScaleFactor = 0.1

           // Add text over the diagonal of the page.
           context.translateBy(x: 0, y: pageRect.size.height / 2)
           context.rotate(by: -.pi / 4)
           let attributes: [NSAttributedString.Key: Any] = [
               .font: UIFont.boldSystemFont(ofSize: 30),
               .foregroundColor: UIColor.red.withAlphaComponent(0.5)
           ]
           text.draw(with: pageRect, options: .usesLineFragmentOrigin, attributes: attributes, context: stringDrawingContext)
       }

        let editedDocumentURl = URL(filePath: NSTemporaryDirectory() + "/document-watermark.pdf")

       let processor = Processor(configuration: configuration, securityOptions: nil)
       do {
           // Write the modified document. `editedDocumentURL` can be used
           // to initialize and present the edited document.
           try processor.write(toFileURL: editedDocumentURl)
       } catch {
           print(error)
       }

       let editedDocument = Document(url: editedDocumentURl)

       // The configuration closure is optional and allows additional customization.
       let pdfController = PDFViewController(document: editedDocument)

       // Present the PDF view controller within a `UINavigationController` to show built-in toolbar buttons.
       present(UINavigationController(rootViewController: pdfController), animated: true)

   }

}

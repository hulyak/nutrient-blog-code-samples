import UIKit
import PSPDFKit
import PSPDFKitUI

class CropViewController: UIViewController {

    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(animated)

        let fileURL = Bundle.main.url(forResource: "YOUR-DOCUMENT", withExtension: "pdf")!
        let document = Document(url: fileURL)

        guard let configuration = Processor.Configuration(document: document) else {
            print("Could not create a processor configuration. The document might be locked or invalid.")
            return
        }

        // Crop the page to 200×200 pt.
        configuration.changeCropBoxForPage(at: 0, to: CGRect(x: 0, y: 0, width: 200, height: 200))

        let editedDocumentURL = URL(filePath: NSTemporaryDirectory() + "/document-cropped.pdf")

        let processor = Processor(configuration: configuration, securityOptions: nil)
        do {
            // Write the modified document. `editedDocumentURL` can be used
            // to initialize and present the edited document.
            try processor.write(toFileURL: editedDocumentURL)
        } catch {
            print(error)
        }

        let editedDocument = Document(url: editedDocumentURL)

        // The configuration closure is optional and allows additional customization.
        let pdfController = PDFViewController(document: editedDocument)

        // Present the PDF view controller within a `UINavigationController` to show built-in toolbar buttons.
        present(UINavigationController(rootViewController: pdfController), animated: true)

    }
}

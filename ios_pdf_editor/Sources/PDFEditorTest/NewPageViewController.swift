import UIKit
import PSPDFKit
import PSPDFKitUI

class NewPageViewController: UIViewController {

    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(animated)

        let fileURL = Bundle.main.url(forResource: "YOUR-DOCUMENT", withExtension: "pdf")!
        let document = Document(url: fileURL)

        guard let configuration = Processor.Configuration(document: document) else {
            print("Could not create a processor configuration. The document might be locked or invalid.")
            return
        }

        // Create a blank page template.
        let pageTemplate = PageTemplate(pageType: .emptyPage, identifier: .blank)
        // Create a new page configuration.
        let newPageConfiguration = PDFNewPageConfiguration(pageTemplate: pageTemplate, builderBlock: nil)
        // Add the blank page at index 1.
        configuration.addNewPage(at: 1, configuration: newPageConfiguration)

        let editedDocumentURl = URL(filePath: NSTemporaryDirectory() + "/document-new-page.pdf")

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

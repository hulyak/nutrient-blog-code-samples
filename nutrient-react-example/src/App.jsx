import PdfViewerComponent from "./components/PdfViewerComponent";

function App() {
  return (
    <div className="App" style={{ width: "100vw" }}>
      <div className="PDF-viewer">
        <PdfViewerComponent document={"document.pdf"} enableSigning={true} />
      </div>
    </div>
  );
}

export default App;

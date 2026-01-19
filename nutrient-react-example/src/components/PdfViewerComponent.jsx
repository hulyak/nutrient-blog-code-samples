import { useEffect, useRef, useCallback } from "react";
import forge from "node-forge";

export default function PdfViewerComponent(props) {
  const containerRef = useRef(null);

  const generatePKCS7 = useCallback(({ fileContents }) => {
    const certificatePromise = fetch("cert.pem").then((response) =>
      response.text(),
    );
    const privateKeyPromise = fetch("private-key.pem").then((response) =>
      response.text(),
    );
    return new Promise((resolve, reject) => {
      Promise.all([certificatePromise, privateKeyPromise])
        .then(([certificatePem, privateKeyPem]) => {
          const certificate = forge.pki.certificateFromPem(certificatePem);
          const privateKey = forge.pki.privateKeyFromPem(privateKeyPem);

          const p7 = forge.pkcs7.createSignedData();
          p7.content = new forge.util.ByteBuffer(fileContents);
          p7.addCertificate(certificate);
          p7.addSigner({
            key: privateKey,
            certificate,
            digestAlgorithm: forge.pki.oids.sha256,
            authenticatedAttributes: [
              {
                type: forge.pki.oids.contentType,
                value: forge.pki.oids.data,
              },
              {
                type: forge.pki.oids.messageDigest,
              },
              {
                type: forge.pki.oids.signingTime,
                value: new Date(),
              },
            ],
          });

          p7.sign({ detached: true });
          const result = stringToArrayBuffer(
            forge.asn1.toDer(p7.toAsn1()).getBytes(),
          );
          resolve(result);
        })
        .catch(reject);
    });
  }, []);

  function stringToArrayBuffer(binaryString) {
    const buffer = new ArrayBuffer(binaryString.length);
    let bufferView = new Uint8Array(buffer);
    for (let i = 0, len = binaryString.length; i < len; i++) {
      bufferView[i] = binaryString.charCodeAt(i);
    }
    return buffer;
  }

  useEffect(() => {
    const container = containerRef.current;
    let NutrientViewer = null;

    (async () => {
      try {
        NutrientViewer = (await import("@nutrient-sdk/viewer")).default;

        NutrientViewer.unload(container); // Ensure there's only one Nutrient instance.

        const instance = await NutrientViewer.load({
          container,
          document: props.document,
          baseUrl: `${window.location.protocol}//${window.location.host}/`,
        });

        console.log("PDF loaded successfully.");

        // Only attempt signing if `enableSigning` prop is `true`.
        if (props.enableSigning) {
          try {
            await instance.signDocument(null, generatePKCS7);
            console.log("Document signed.");
          } catch (signError) {
            console.warn("Could not sign document:", signError.message);
          }
        }
      } catch (error) {
        console.error("Failed to load PDF:", error);
      }
    })();

    return () => {
      if (NutrientViewer) {
        NutrientViewer.unload(container);
      }
    };
  }, [generatePKCS7, props.document, props.enableSigning]);

  return <div ref={containerRef} style={{ width: "100%", height: "100vh" }} />;
}

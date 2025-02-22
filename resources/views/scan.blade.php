<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
</head>
<body>
    <h2>Scan a QR Code</h2>
    <video id="video" autoplay playsinline style="width: 100%; max-width: 400px;"></video>
    <p id="result">Scanning...</p>
    <button id="startScan">Start Scanning</button>

    <script>
        async function useBarcodeDetectionAPI() {
            const video = document.getElementById("video");
            const resultText = document.getElementById("result");

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
                video.srcObject = stream;

                const barcodeDetector = new BarcodeDetector({ formats: ["qr_code"] });

                async function scanFrame() {
                    try {
                        const barcodes = await barcodeDetector.detect(video);
                        if (barcodes.length > 0) {
                            const url = barcodes[0].rawValue;
                            resultText.textContent = "Redirecting to: " + url;
                            window.location.href = url;
                        } else {
                            requestAnimationFrame(scanFrame);
                        }
                    } catch (error) {
                        console.error("Barcode detection error:", error);
                    }
                }

                video.addEventListener("loadeddata", () => {
                    scanFrame();
                });

            } catch (err) {
                console.error("Camera access error:", err);
                alert("Error accessing camera.");
            }
        }

        async function useQrScannerLibrary() {
            const script = document.createElement("script");
            script.src = "https://unpkg.com/qr-scanner/qr-scanner.min.js";
            document.body.appendChild(script);

            script.onload = async () => {
                const QrScanner = window.QrScanner;
                const video = document.getElementById("video");
                const resultText = document.getElementById("result");

                const qrScanner = new QrScanner(video, result => {
                    resultText.textContent = "Redirecting to: " + result;
                    window.location.href = result;
                });

                try {
                    await qrScanner.start();
                } catch (err) {
                    console.error("QR Scanner error:", err);
                    alert("Error starting QR scanner.");
                }
            };
        }

        document.getElementById("startScan").addEventListener("click", () => {
            if ("BarcodeDetector" in window) {
                console.log("Using Barcode Detection API");
                useBarcodeDetectionAPI();
            } else {
                console.log("Barcode Detection API not supported. Using QR Scanner library.");
                useQrScannerLibrary();
            }
        });
    </script>
</body>
</html>

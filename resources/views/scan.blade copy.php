<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
</head>
<body>
    <h2>Scan a QR Code</h2>
    <video id="qr-video" style="width: 100%; max-width: 400px;"></video>
    <p id="result">Scanning...</p>

    <script type="module">
        import QrScanner from "https://unpkg.com/qr-scanner/qr-scanner.min.js";

        const video = document.getElementById("qr-video");
        const resultText = document.getElementById("result");

        const qrScanner = new QrScanner(video, result => {
            resultText.textContent = "Redirecting to: " + result;
            window.location.href = result; // Redirects to scanned URL
        });

        qrScanner.start();

        // Stop the scanner when user leaves the page
        window.addEventListener("beforeunload", () => {
            qrScanner.stop();
        });
    </script>
</body>
</html>

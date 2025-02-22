import QrScanner from "qr-scanner";

const video = document.getElementById("qr-video");
const resultText = document.getElementById("result");

const qrScanner = new QrScanner(video, (result) => {
    resultText.textContent = "Redirecting to: " + result;
    window.location.href = result; // Redirects to scanned URL
});

qrScanner.start();

// Stop the scanner when the user leaves the page
window.addEventListener("beforeunload", () => {
    qrScanner.stop();
});

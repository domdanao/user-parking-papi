<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator with Logo</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 20px;
        }

        #qr-preview {
            max-width: 300px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        canvas {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>QR Code Generator</h1>
        
        <div class="input-group">
            <label for="url">Enter URL:</label>
            <input type="text" id="url" placeholder="https://example.com">
        </div>

        <div class="input-group">
            <label for="logo">Upload Logo (optional):</label>
            <input type="file" id="logo" accept="image/png,image/jpeg">
        </div>

        <div class="preview" id="preview-container" style="display: none;">
            <img id="qr-preview" alt="Generated QR Code">
            <button id="download">Download QR Code</button>
        </div>
    </div>

    <canvas id="qr-canvas"></canvas>

    <script>
        const urlInput = document.getElementById('url');
        const logoInput = document.getElementById('logo');
        const canvas = document.getElementById('qr-canvas');
        const ctx = canvas.getContext('2d');
        const previewContainer = document.getElementById('preview-container');
        const previewImg = document.getElementById('qr-preview');
        const downloadBtn = document.getElementById('download');

        let currentLogo = null;

        function generateQR() {
            const url = urlInput.value;
            if (!url) return;

            // Create QR Code
            const qr = qrcode(0, 'H'); // Highest error correction level
            qr.addData(url);
            qr.make();

            // Set canvas size
            const moduleCount = qr.getModuleCount();
            const tileSize = 8; // Size of each QR code module
            const size = moduleCount * tileSize;
            canvas.width = size;
            canvas.height = size;

            // Clear canvas with transparency
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw QR code
            for (let row = 0; row < moduleCount; row++) {
                for (let col = 0; col < moduleCount; col++) {
                    if (qr.isDark(row, col)) {
                        ctx.fillStyle = '#000000';
                        ctx.fillRect(col * tileSize, row * tileSize, tileSize, tileSize);
                    }
                }
            }

            // If logo exists, draw it
            if (currentLogo) {
                const logoImg = new Image();
                logoImg.onload = () => {
                    // Calculate logo size (25% of QR code size)
                    const logoSize = size * 0.25;
                    const logoX = (size - logoSize) / 2;
                    const logoY = (size - logoSize) / 2;

                    // Draw logo without circular masking
                    ctx.drawImage(logoImg, logoX, logoY, logoSize, logoSize);

                    updatePreview();
                };
                logoImg.src = currentLogo;
            } else {
                updatePreview();
            }
        }

        function updatePreview() {
            // Convert canvas to PNG data URL
            const dataUrl = canvas.toDataURL('image/png');
            previewImg.src = dataUrl;
            previewContainer.style.display = 'flex';
        }

        // Event Listeners
        urlInput.addEventListener('input', generateQR);

        logoInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    currentLogo = e.target.result;
                    generateQR();
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        downloadBtn.addEventListener('click', () => {
            const link = document.createElement('a');
            link.download = 'qr-code.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    </script>
</body>
</html>
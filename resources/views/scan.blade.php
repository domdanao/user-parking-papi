<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Scan QR</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        #reader {
            width: 100%;
            height: 100vh;
        }

        #status {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
	<div id="status">Waiting for camera access...</div>
    <div id="reader"></div>

    <script>
        const status = document.getElementById('status');
        
        function onScanSuccess(decodedText) {
            // Stop the scanner
            html5QrcodeScanner.clear();
            
            // Update status
            status.textContent = 'QR Code detected! Redirecting...';
            
            // Redirect to the decoded URL
            setTimeout(() => {
                window.location.href = decodedText;
            }, 1000);
        }

        function onScanError(error) {
            // Handle scan error (we don't need to show errors to users)
            console.warn(error);
        }

        // Configure scanner options
        let config = {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            },
            aspectRatio: 1.0
        };

        // Initialize the scanner
        const html5QrcodeScanner = new Html5Qrcode("reader");
        
        // Start scanning
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                // Start scanning with the first available camera
                html5QrcodeScanner.start(
                    { facingMode: "environment" }, // Prefer back camera
                    config,
                    onScanSuccess,
                    onScanError
                ).then(() => {
                    status.textContent = 'Scanning for QR codes...';
                }).catch(err => {
                    status.textContent = 'Error starting scanner: ' + err;
                });
            } else {
                status.textContent = 'No cameras found';
            }
        }).catch(err => {
            status.textContent = 'Error accessing camera: ' + err;
        });
    </script>
</body>
</html>
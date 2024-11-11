<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Kaartjes</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcode"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-database.js"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .form { display: none; margin-top: 20px; }
        .output { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>QR Kaartjes Generator</h1>
    <button onclick="generateQRCode()">Genereer QR Code</button>
    <div id="qrcode"></div>

    <div class="form" id="form-container">
        <h2>Vul je gegevens in</h2>
        <input type="text" id="name" placeholder="Naam"><br>
        <input type="email" id="email" placeholder="E-mail"><br>
        <button onclick="submitForm()">Verzenden</button>
    </div>

    <div class="output" id="output-container"></div>

    <script>
        // Firebase configuratie
        const firebaseConfig = {
            apiKey: "AIzaSyAPimbCCvjjAIKyzLKutfz62CGEL8x-6Ng",
            authDomain: "gala-54873.firebaseapp.com",
            databaseURL: "https://gala-54873.firebaseio.com",
            projectId: "gala-54873",
            storageBucket: "gala-54873.appspot.com",
            messagingSenderId: "883917821306",
            appId: "JOUW_APP_ID"
        };
        firebase.initializeApp(firebaseConfig);
        const database = firebase.database();

        // Functie om een QR code te genereren
        function generateQRCode() {
            const id = Date.now().toString();
            const url = window.location.href + "?id=" + id;
            new QRCode(document.getElementById("qrcode"), url);
            database.ref('scans/' + id).set({
                scanned: 0,
                data: null
            });
        }

        // Controleer op scan en toon formulier of gegevens
        const urlParams = new URLSearchParams(window.location.search);
        const scanId = urlParams.get('id');
        if (scanId) {
            const scanRef = database.ref('scans/' + scanId);
            scanRef.once('value').then(snapshot => {
                const scanData = snapshot.val();
                if (scanData && scanData.scanned === 0) {
                    document.getElementById("form-container").style.display = "block";
                } else if (scanData && scanData.scanned > 0) {
                    document.getElementById("output-container").innerHTML = `
                        <h2>Gegevens</h2>
                        <p>Naam: ${scanData.data.name}</p>
                        <p>Email: ${scanData.data.email}</p>
                        <p>Aantal keer gescand: ${scanData.scanned + 1}</p>
                    `;
                    scanRef.update({ scanned: scanData.scanned + 1 });
                }
            });
        }

        // Verzend gegevens en toon ze bij volgende scans
        function submitForm() {
            const name = document.getElementById("name").value;
            const email = document.getElementById("email").value;
            const scanRef = database.ref('scans/' + scanId);
            scanRef.update({
                data: { name: name, email: email },
                scanned: 1
            }).then(() => {
                document.getElementById("output-container").innerHTML = `
                    <h2>Gegevens opgeslagen!</h2>
                    <p>Naam: ${name}</p>
                    <p>Email: ${email}</p>
                    <p>Aantal keer gescand: 1</p>
                `;
                document.getElementById("form-container").style.display = "none";
            });
        }
    </script>
</body>
</html>

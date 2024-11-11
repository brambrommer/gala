<?php
// Databaseconfiguratie
$host = "sql103.infinityfree.com"; // Pas aan naar jouw gegevens
$dbname = "if0_37684458_test";  // Pas aan naar jouw gegevens
$username = "if0_37684458";         // Pas aan naar jouw gegevens
$password = "QpMIhRNkiEXUjO"; // Pas aan naar jouw gegevens

// Maak verbinding met de database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout bij verbinden met database: " . $e->getMessage());
}

// URL ID ophalen
$url_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($url_id) {
    // Controleer of het ID al bestaat in de database
    $stmt = $pdo->prepare("SELECT * FROM gebruikers WHERE url_id = :url_id");
    $stmt->execute(['url_id' => $url_id]);
    $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gebruiker) {
        // Verhoog bezoek_teller
        $bezoek_teller = $gebruiker['bezoek_teller'] + 1;
        
        // Update bezoek_teller in de database
        $update_stmt = $pdo->prepare("UPDATE gebruikers SET bezoek_teller = :bezoek_teller WHERE url_id = :url_id");
        $update_stmt->execute(['bezoek_teller' => $bezoek_teller, 'url_id' => $url_id]);

        if ($bezoek_teller == 2) {
            // Toon gegevens als het de tweede keer is
            echo "<h1>Welkom terug!</h1>";
            echo "<p>Naam: " . htmlspecialchars($gebruiker['naam']) . "</p>";
            echo "<p>Leerlingnummer: " . htmlspecialchars($gebruiker['leerlingnummer']) . "</p>";
        } elseif ($bezoek_teller > 2) {
            // Toon bericht als het de derde keer of meer is
            echo "<p>Deze link is al gebruikt.</p>";
        }
    } else {
        // Eerste bezoek, toon formulier
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Gegevens opslaan in de database
            $naam = $_POST['naam'];
            $leerlingnummer = $_POST['leerlingnummer'];
            
            $insert_stmt = $pdo->prepare("INSERT INTO gebruikers (url_id, naam, leerlingnummer) VALUES (:url_id, :naam, :leerlingnummer)");
            $insert_stmt->execute([
                'url_id' => $url_id,
                'naam' => $naam,
                'leerlingnummer' => $leerlingnummer
            ]);

            echo "<p>Bedankt! Jouw gegevens zijn opgeslagen.</p>";
        } else {
            // HTML formulier weergeven
            echo '
            <!DOCTYPE html>
            <html lang="nl">
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Gegevens invullen</title>
            </head>
            <body>
                <h1>Vul je gegevens in</h1>
                <form method="post" action="">
                    <label for="naam">Naam:</label><br>
                    <input type="text" id="naam" name="naam" required><br><br>
                    <label for="leerlingnummer">Leerlingnummer:</label><br>
                    <input type="text" id="leerlingnummer" name="leerlingnummer" required><br><br>
                    <input type="submit" value="Opslaan">
                </form>
            </body>
            </html>
            ';
        }
    }
} else {
    echo "Geen geldig ID opgegeven in de URL.";
}
?>

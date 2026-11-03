
<?php
$errore = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim(htmlspecialchars($_POST["name"]));
    $peso = $_POST["weight"];
    $altezza = $_POST["height"];

    if ($nome == "" || !is_numeric($peso) || !is_numeric($altezza) || $peso <= 0 || $altezza <= 0) {

        $errore = "Inserisci valori validi";

    } else {

        // Calcolo BMI
        $altezza_m = $altezza / 100;
        $bmi = $peso / ($altezza_m * $altezza_m);

        // Classificazione
        if ($bmi < 18.5) {
            $classe = "sottopeso";
            $fascia = "Sottopeso";
        } elseif ($bmi <= 24.9) {
            $classe = "normopeso";
            $fascia = "Normopeso";
        } elseif ($bmi <= 29.9) {
            $classe = "sovrappeso";
            $fascia = "Sovrappeso";
        } else {
            $classe = "obesita";
            $fascia = "Obesità";
        }
    }

    // Salvataggio CSV
    $log = fopen("log.csv", "a");
    if ($log) {
        $record = date("d/m/Y").",".date("H:i:s").",".$_SERVER["REMOTE_ADDR"].",".$nome.",".$peso.",".$altezza.",".round($bmi, 2).",".$fascia.PHP_EOL;
        fwrite($log, $record);
        fclose($log);
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatore BMI</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>Calcolatore del BMI</h1>

    <?php if ($_SERVER["REQUEST_METHOD"] != "POST" || $errore): ?>

        <div class="box">

            <?php if ($errore): ?>
                <p><?php echo $errore; ?></p>
            <?php endif; ?>

            <form method="POST">

                <p>
                    <label for="name">Nome:</label>
                    <input type="text" id="name" name="name">
                </p>

                <p>
                    <label for="weight">Peso (in kg):</label>
                    <input type="text" id="weight" name="weight">
                </p>

                <p>
                    <label for="height">Altezza (in cm):</label>
                    <input type="text" id="height" name="height">
                </p>

                <p>
                    <input type="submit" value="Calcola BMI">
                </p>

            </form>

        </div>

    <?php else: ?>

        <div class="box <?php echo $classe; ?>">

            <div class="nome-utente">
                <?php echo $nome; ?>
            </div>

            <div class="dati-inseriti">
                <?php echo $peso; ?> kg, <?php echo $altezza; ?> cm
            </div>

            <div class="valore-bmi">
                <?php echo number_format($bmi, 2, ',', ''); ?>
            </div>

            <div class="testo-fascia">
                <?php echo $fascia; ?>
            </div>

            <br>

            <a href="index.php" class="link-riprova">
                Calcola di nuovo
            </a>

        </div>

    <?php endif; ?>

</body>
</html>
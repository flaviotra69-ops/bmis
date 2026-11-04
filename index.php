
<?php
$errore_nome = false;
$errore_peso = false;
$errore_altezza = false;

$nome = "";
$peso = "";
$altezza = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim(htmlspecialchars($_POST["name"]));
    $peso = $_POST["weight"];
    $altezza = $_POST["height"];

    // Controllo dei singoli campi
    if ($nome == "") {
        $errore_nome = true;
    }

    if (!is_numeric($peso) || $peso <= 0) {
        $errore_peso = true;
    }

    if (!is_numeric($altezza) || $altezza <= 0) {
        $errore_altezza = true;
    }

    // Calcolo BMI solo se tutti i campi sono validi
    if (!$errore_nome && !$errore_peso && !$errore_altezza) {

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

        // Salvataggio CSV
        $log = fopen("log.csv", "a");

        if ($log) {
            $record = date("d/m/Y").",".date("H:i:s").",".$_SERVER["REMOTE_ADDR"].",".$nome.",".$peso.",".$altezza.",".round($bmi, 2).",".$fascia.PHP_EOL;

            fwrite($log, $record);
            fclose($log);
        }
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

    <?php if ($_SERVER["REQUEST_METHOD"] != "POST" || $errore_nome || $errore_peso || $errore_altezza): ?>

        <div class="box">

            <form method="POST">

                <p>
                    <label for="name">Nome:</label>

                    <input type="text" id="name" name="name"
                    value="<?php echo $nome; ?>">

                    <?php if ($errore_nome): ?>
                        <br>
                        <span>Valore non valido</span>
                    <?php endif; ?>
                </p>

                <p>
                    <label for="weight">Peso (in kg):</label>

                    <input type="text" id="weight" name="weight"
                    value="<?php echo htmlspecialchars($peso); ?>">

                    <?php if ($errore_peso): ?>
                        <br>
                        <span>Valore non valido</span>
                    <?php endif; ?>
                </p>

                <p>
                    <label for="height">Altezza (in cm):</label>

                    <input type="text" id="height" name="height"
                    value="<?php echo htmlspecialchars($altezza); ?>">

                    <?php if ($errore_altezza): ?>
                        <br>
                        <span>Valore non valido</span>
                    <?php endif; ?>
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
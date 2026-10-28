<?php
$errore = null;
$nome = "";
$peso = "";
$altezza_cm = "";
$bmi = 0;
$classe_fascia = "";
$nome_fascia = "";
// Controlla se la pagina è stata richiamata tramite il metodo POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recupera il nome inviato dal form, elimina gli spazi iniziali e finali
    // e converte i caratteri speciali in entità HTML.
    $nome = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
    // Recupera il peso inviato dal form, se il campo esiste.
    $peso_input = isset($_POST['weight']) ? trim($_POST['weight']) : '';
    // Recupera l'altezza inviata dal form, se il campo esiste.
    $altezza_input = isset($_POST['height']) ? trim($_POST['height']) : '';
    // Controlla se il peso è numerico; se lo è, lo converte in numero decimale.
    // Altrimenti assegna zero alla variabile $peso.

    $peso = is_numeric($peso_input) ? floatval($peso_input) : 0;
    // Controlla se l'altezza è numerica e la converte in numero decimale.
    // Se non è numerica, assegna zero alla variabile $altezza_cm.
    $altezza_cm = is_numeric($altezza_input) ? floatval($altezza_input) : 0;
    $nome_valido = !empty($nome);
    $peso_valido = is_numeric($peso_input) && $peso > 0;
    $altezza_valida = is_numeric($altezza_input) && $altezza_cm > 0;
    // Se il campo è valido, prepara il relativo nome per il log
    // Altrimenti assegna la stringa NOT_VALID.
    $log_nome = $nome_valido ? $nome : "NOT_VALID";
    $log_peso = $peso_valido ? $peso : "NOT_VALID";
    $log_altezza = $altezza_valida ? $altezza_cm : "NOT_VALID";
    // Controlla se almeno uno dei tre dati inseriti non è valido.
    if (!$nome_valido || !$peso_valido || !$altezza_valida) {
        // Printare il messaggio di errore da visualizzare nella pagina.
        $errore = "Inserisci valori validi";
        // Registra NOT_VALID per il BMI perché non può essere calcolato.
        $log_bmi = "NOT_VALID";
        // Registra NOT_VALID per la fascia perché non può essere determinata.
        $log_fascia = "NOT_VALID";
    } else {
        // Converte l'altezza da centimetri a metri dividendo per 100.
        $altezza_m = $altezza_cm / 100;
        // Calcola il BMI dividendo il peso per il quadrato dell'altezza in metri.
        $bmi = $peso / ($altezza_m * $altezza_m);
        // Controlla se il BMI è inferiore a 18,5.
        if ($bmi < 18.5) {
            // Assegna la classe CSS relativa al sottopeso.
            $classe_fascia = "sottopeso";
            // Assegna il testo da visualizzare nella pagina.
            $nome_fascia = "Sottopeso";
        // Se la condizione precedente è falsa, controlla se il BMI è al massimo 24,9.
        } elseif ($bmi <= 24.9) {
            // Assegna la classe CSS relativa al normopeso.
            $classe_fascia = "normopeso";
            // Assegna il testo da visualizzare nella pagina.
            $nome_fascia = "Normopeso";
        // Se le condizioni precedenti sono false, controlla se il BMI è al massimo 29,9.
        } elseif ($bmi <= 29.9) {
            // Assegna la classe CSS relativa al sovrappeso.
            $classe_fascia = "sovrappeso";
            // Assegna il testo da visualizzare nella pagina.
            $nome_fascia = "Sovrappeso";
        // Se nessuna delle condizioni precedenti è vera, il BMI è almeno 30.
        } else {
            // Assegna la classe CSS relativa alla categoria obesità.
            $classe_fascia = "obesita";
            // Assegna il testo da visualizzare nella pagina.
            $nome_fascia = "Obesità";
        }
        // Arrotonda il BMI a due cifre decimali per registrarlo nel log.
        $log_bmi = round($bmi, 2);
        // Salva nel log il nome della classe assegnata al risultato.
        $log_fascia = $classe_fascia;
    }
    // Apre il file log.csv in modalità append per aggiungere un nuovo record.
    $log = fopen("log.csv", "a");
    // Controlla se il file è stato aperto correttamente.
    if ($log) {

        // Costruisce una riga CSV con data, ora, IP, nome, peso, altezza, BMI e fascia.
        // PHP_EOL aggiunge un'interruzione di riga, per andare a capo
        $record = date("d/m/Y").",".date("H:i:s").",".$_SERVER["REMOTE_ADDR"].",".$log_nome.",".$log_peso.",".$log_altezza.",".$log_bmi.",".$log_fascia.PHP_EOL;

        // Scrive la riga costruita all'interno del file CSV.
        fwrite($log, $record);
        // Chiude il file dopo aver scritto il record.
        fclose($log);
    }

// Se la pagina non è stata richiamata tramite POST, esegue il blocco else.
} else {
    // Reindirizza l'utente alla pagina iniziale.
    header('Location: index.html');
    // Interrompe l'esecuzione del programma dopo il reindirizzamento.
    exit;
}
?>

<!DOCTYPE html>
<!-- Indica che il documento utilizza HTML5 e la lingua italiana. -->
<html lang="it">

<head>
    <!-- Definisce la codifica dei caratteri utilizzata nella pagina. -->
    <meta charset="UTF-8">

    <!-- Adatta la larghezza della pagina al dispositivo utilizzato. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Definisce il titolo mostrato nella scheda del browser. -->
    <title>Risultato BMI</title>

    <!-- Collega il foglio di stile esterno alla pagina. -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Controlla se è presente un messaggio di errore. -->
    <?php if ($errore): ?>

        <!-- Visualizza il contenitore con lo stile previsto per l'errore. -->
        <div class="box obesita">

            <!-- Mostra il titolo della pagina di errore. -->
            <h1>Errore</h1>

            <!-- Mostra il messaggio di errore in rosso e in grassetto. -->
            <p style="font-weight: bold; color: #B32D2D;">
                <?php echo $errore; ?>
            </p>

            <!-- Permette di tornare al modulo di inserimento. -->
            <a href="index.html" class="link-riprova">
                Torna indietro
            </a>

        </div>

    <!-- Se non ci sono errori, mostra il risultato del calcolo. -->
    <?php else: ?>

        <!-- Crea la card e assegna la classe CSS corrispondente alla fascia. -->
        <div class="box <?php echo $classe_fascia; ?>">

            <!-- Visualizza il nome dell'utente. -->
            <div class="nome-utente">
                <?php echo $nome; ?>
            </div>

            <!-- Visualizza il peso e l'altezza inseriti. -->
            <div class="dati-inseriti">
                <?php echo $peso; ?> kg, <?php echo $altezza_cm; ?> cm
            </div>

            <!-- Visualizza il BMI con due cifre decimali e la virgola. -->
            <div class="valore-bmi">
                <?php echo number_format($bmi, 2, ',', ''); ?>
            </div>

            <!-- Visualizza il nome descrittivo della fascia. -->
            <div class="testo-fascia">
                <?php echo $nome_fascia; ?>
            </div>

            <!-- Inserisce un'interruzione di riga HTML. -->
            <br>

            <!-- Permette di tornare al modulo per eseguire un nuovo calcolo. -->
            <a href="index.html" class="link-riprova">
                Calcola di nuovo
            </a>

        <!-- Chiude il contenitore del risultato. -->
        </div>

    <!-- Chiude la condizione PHP. -->
    <?php endif; ?>

<!-- Chiude il corpo della pagina HTML. -->
</body>

<!-- Chiude il documento HTML. -->
</html>

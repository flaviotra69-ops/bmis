<?php
// Inizializzazione della variabile per eventuali messaggi di errore
$errore = null;
$nome = "";
$peso = "";
$altezza_cm = "";
$bmi = 0;
$classe_fascia = "";
$nome_fascia = "";

// Verifica se la pagina è stata richiamata inviando i dati tramite il metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // se presente, rimuove spazi e converte caratteri speciali
    $nome = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
    // Recupera il campo 'weight' e lo converte in un numero decimale, se assente imposta 0
    $peso = isset($_POST['weight']) ? floatval($_POST['weight']) : 0;
    // Recupera il campo 'height' e lo converte in un numero decimale,  se assente imposta 0
    $altezza_cm = isset($_POST['height']) ? floatval($_POST['height']) : 0;
    // Controlla se i dati inseriti sono validi
    if (empty($nome) || $peso <= 0 || $altezza_cm <= 0) {
        
        // Se uno dei dati è errato, memorizza il messaggio di errore da mostrare all'utente
        $errore = "Inserisci valori validi";
        // "NOT_VALID" da scrivere nel log CSV per i campi non corretti
        $log_nome = empty($nome) ? "NOT_VALID" : $nome;
        $log_peso = ($peso <= 0) ? "NOT_VALID" : $peso;
        $log_altezza = ($altezza_cm <= 0) ? "NOT_VALID" : $altezza_cm;
        $log_bmi = "NOT_VALID";
        $log_fascia = "NOT_VALID";

    } else {
        // Se i dati sono corretti: converte l'altezza da centimetri a metri per la formula del BMI
        $altezza_m = $altezza_cm / 100;

        // Calcola l'indice di massa corporea con la formula: Peso / (Altezza * Altezza)
        $bmi = $peso / ($altezza_m * $altezza_m);

        // Classificazione del valore BMI ottenuto nelle varie categorie di peso
        if ($bmi < 18.5) {
            // Caso BMI minore di 18.5
            $classe_fascia = "sottopeso"; // Nome della classe CSS per lo stile visuale
            $nome_fascia   = "Sottopeso"; // Testo descrittivo stampato a schermo
        } elseif ($bmi <= 24.9) {
            // Caso BMI compreso tra 18.5 e 24.9
            $classe_fascia = "normopeso";
            $nome_fascia   = "Normopeso";
        } elseif ($bmi <= 29.9) {
            // Caso BMI compreso tra 25.0 e 29.9
            $classe_fascia = "sovrappeso";
            $nome_fascia   = "Sovrappeso";
        } else {
            // Caso BMI maggiore o uguale a 30.0
            $classe_fascia = "obesita";
            $nome_fascia   = "Obesità";
        }

        // Assegna i valori validi formattati pronti per essere salvati nel CSV
        $log_nome = $nome;
        $log_peso = $peso;
        $log_altezza = $altezza_cm;
        $log_bmi = round($bmi, 2); // Arrotonda il BMI a 2 cifre decimali
        $log_fascia = $classe_fascia;
    }

    // Apre il file "log.csv" in modalità 'append' ("a") per aggiungere la nuova riga alla fine del file
    $log = fopen("log.csv", "a");

    if ($log) {
        $record = date("d/m/Y").",".date("H:i:s").",".$_SERVER["REMOTE_ADDR"].",".$log_nome.",".$log_peso.",".$log_altezza.",".$log_bmi.",".$log_fascia;
        fwrite($log, $record);

        fclose($log);
    }
} else {
    // Se l'utente tenta di accedere direttamente alla pagina calcola.php senza inviare il form, viene reindirizzato alla home
    header('Location: index.html');
    exit; // Interrompe l'esecuzione dello script dopo il reindirizzamento
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risultato BMI</title>
    <!-- Collegamento al foglio di stile esterno CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Controllo se è presente un errore di inserimento dati -->
    <?php if ($errore): ?>
        <!-- Blocco HTML per la visualizzazione del messaggio di errore -->
        <div class="box obesita">
            <h1>Errore</h1>
            <p style="font-weight: bold; color: #B32D2D;"><?php echo $errore; ?></p>
            <a href="index.html" class="link-riprova">Torna indietro</a>
        </div>
    <?php else: ?>
        <!-- Blocco HTML per la visualizzazione della card con il risultato finale -->
        <div class="box <?php echo $classe_fascia; ?>">
            <!-- Stampa il nome inserito dall'utente -->
            <div class="nome-utente"><?php echo $nome; ?></div>

            <!-- Stampa i dati di peso e altezza inseriti -->
            <div class="dati-inseriti"><?php echo $peso; ?> kg, <?php echo $altezza_cm; ?> cm</div>

            <!-- Formatta il BMI con la virgola decimali e 2 cifre e lo mostra a schermo -->
            <div class="valore-bmi"><?php echo number_format($bmi, 2, ',', ''); ?></div>

            <!-- Stampa il nome della fascia (es. Sottopeso, Normopeso...) -->
            <div class="testo-fascia"><?php echo $nome_fascia; ?></div>

            <br>
            <!-- Pulsante per tornare al form e calcolare nuovamente -->
            <a href="index.html" class="link-riprova">Calcola di nuovo</a>
        </div>
    <?php endif; ?>

</body>
</html>
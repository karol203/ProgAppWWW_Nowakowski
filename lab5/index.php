<?php
// Pseudokod wyboru widoku:
// 1. Zdefiniuj mape dostepnych podstron ($strony): klucz => [tytul, sciezka_w_html].
// 2. Odczytaj parametr $_GET['idp']. Jesli brak parametru lub pusty tekst '' -> ustaw klucz na 'glowna'.
// 3. Jesli $_GET['idp'] nie jest tekstem (np. tablica ?idp[]=filmy) lub klucza nie ma w mapie:
//    -> ustaw kod HTTP 404, zapisz komunikat bledu 404 i nie wczytuj pliku.
// 4. Jesli klucz istnieje w mapie -> zbuduj pelna sciezke przez __DIR__ i wartosc z mapy.
// 5. Sprawdz czy plik istnieje i da sie odczytac: is_file() oraz is_readable().
//    -> Jesli NIE: ustaw kod HTTP 500, pokaz komunikat bledu serwera (bez ujawniania sciezki systemowej).
//    -> Jesli TAK: zapamietaj bezpieczna sciezke do dolaczenia przez include w <main>.

$idProjektu = 'sklep-demo';

$strony = [
    'glowna' => [
        'tytul' => 'Strona Główna',
        'plik' => 'html/glowna.html'
    ],
    'produkt' => [
        'tytul' => 'Produkt',
        'plik' => 'html/produkt.html'
    ],
    'kontakt' => [
        'tytul' => 'Kontakt',
        'plik' => 'html/kontakt.html'
    ],
    'filmy' => [
        'tytul' => 'Filmy',
        'plik' => 'html/filmy.html'
    ]
];

$aktywnyKlucz = 'glowna';
$kodBledu = 200;
$komunikatBledu = '';
$plikDoWczytania = '';
$tytulWidoku = '';

if (!isset($_GET['idp']) or $_GET['idp'] === '') {
    $aktywnyKlucz = 'glowna';
} elseif (!is_string($_GET['idp']) or !array_key_exists($_GET['idp'], $strony)) {
    http_response_code(404);
    $kodBledu = 404;
    $aktywnyKlucz = '';
    $tytulWidoku = '404 Nie znaleziono podstrony';
    $komunikatBledu = 'Błąd 404: Podana podstrona nie istnieje.';
} else {
    $aktywnyKlucz = $_GET['idp'];
}

if ($kodBledu === 200) {
    $wybranaStrona = $strony[$aktywnyKlucz];
    $tytulWidoku = $wybranaStrona['tytul'];
    $pelnaSciezka = __DIR__ . '/' . $wybranaStrona['plik'];

    if (!is_file($pelnaSciezka) or !is_readable($pelnaSciezka)) {
        http_response_code(500);
        $kodBledu = 500;
        $tytulWidoku = '500 Błąd serwera';
        $komunikatBledu = 'Błąd 500: Wybrany widok jest chwilowo niedostępny po stronie serwera.';
    } else {
        $plikDoWczytania = $pelnaSciezka;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tytulWidoku, ENT_QUOTES, 'UTF-8'); ?> - Sklep komputerowy</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="vendor/jquery-4.0.0.min.js" defer></script>
    <script src="js/main.js" defer></script>
    <script src="js/lab3.js" defer></script>
</head>
<body>

    <header class="top-bar">
        <div class="top-lewa">
            <h1>Sklep z akcesoriami komputerowymi</h1>
            <p>Widok: <strong><?php echo htmlspecialchars($tytulWidoku, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        </div>
        <div class="top-prawa">
            <div class="motyw-kontener">
                <button type="button" id="theme-toggle">Tryb jasny</button>
            </div>
            <p id="zegar"></p>
            <div class="zegar-kontener">
                <p id="clock-display">00:00:00</p>
                <button type="button" id="start-btn">Start</button>
                <button type="button" id="stop-btn">Stop</button>
            </div>
        </div>
    </header>

    <nav>
        <ul class="ul-nav">
            <?php foreach ($strony as $klucz => $dane) { ?>
                <li>
                    <h2>
                        <a href="index.php?idp=<?php echo htmlspecialchars($klucz, ENT_QUOTES, 'UTF-8'); ?>"<?php if ($aktywnyKlucz === $klucz and $kodBledu === 200) echo ' aria-current="page"'; ?>>
                            <?php echo htmlspecialchars($dane['tytul'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </h2>
                </li>
            <?php } ?>
            <li>
                <h2><a href="lab4.php">Katalog PHP</a></h2>
            </li>
        </ul>
    </nav>

    <main>
        <?php if ($kodBledu !== 200) { ?>
            <section class="karta" role="alert">
                <h2><?php echo htmlspecialchars($tytulWidoku, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($komunikatBledu, ENT_QUOTES, 'UTF-8'); ?></p>
                <p><a href="index.php">Powrót do strony głównej</a></p>
            </section>
        <?php } else { ?>
            <?php include $plikDoWczytania; ?>
        <?php } ?>
    </main>

    <footer>
        <hr>
        <p>Identyfikator projektu: <strong><?php echo htmlspecialchars($idProjektu, ENT_QUOTES, 'UTF-8'); ?></strong> | &copy; 2026 Sklep z akcesoriami komputerowymi</p>
    </footer>

</body>
</html>
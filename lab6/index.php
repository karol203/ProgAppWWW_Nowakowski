<?php
// Pseudokod odczytu strony z bazy danych:
// 1. Odczytaj parametr $_GET['idp']. Jesli brak lub pusty string '' -> ustaw alias na 'glowna'.
// 2. Sprawdz poprawnosc formatu aliasu: musi byc tekstem i pasowac do wzorca ^[a-z0-9-]{1,20}$ (1-20 malych liter, cyfr lub lacznikow).
//    -> Jesli bledny format lub tablica ?idp[]=... -> ustaw kod HTTP 404 (bez odpytywania bazy).
// 3. Polacz z baza przez cfg.php (PDO) i w bloku try-catch:
//    a) Pobierz liste opublikowanych stron do menu: SELECT alias, page_title FROM page_list WHERE status = 1 ORDER BY id.
//    b) Jesli format aliasu byl poprawny -> wywolaj PokazPodstrone($pdo, $alias).
//       - Gdy zwroci null (brak rekordu lub status = 0) -> ustaw kod HTTP 404 i komunikat bledu.
//       - Gdy zwroci rekord -> ustaw tytul i tresc HTML strony (kod HTTP 200).
// 4. W przypadku wyjatku bazy danych (PDOException / Throwable):
//    -> Zapisz szczegoly awarii do lokalnego pliku logs/error.log (nie wyswietlaj ich w HTML).
//    -> Ustaw kod HTTP 500 i wyswietl ogolny komunikat bledu serwera.

$idProjektu = 'sklep-demo';
$wersjaProjektu = 'v1.5';

$kodHttp = 200;
$aktywnyAlias = 'glowna';
$tytulWidoku = '';
$trescWidoku = '';
$komunikatBledu = '';
$pozycjeMenu = [];

if (!isset($_GET['idp']) or $_GET['idp'] === '') {
    $aktywnyAlias = 'glowna';
} elseif (!is_string($_GET['idp']) or preg_match('/^[a-z0-9-]{1,20}$/', $_GET['idp']) !== 1) {
    $kodHttp = 404;
    $aktywnyAlias = '';
    $tytulWidoku = '404 Nie znaleziono podstrony';
    $komunikatBledu = 'Błąd 404: Nieprawidłowy adres lub podana podstrona nie istnieje.';
} else {
    $aktywnyAlias = $_GET['idp'];
}

try {
    require_once __DIR__ . '/cfg.php';
    require_once __DIR__ . '/showpage.php';

    $stmtMenu = $pdo->query('SELECT alias, page_title FROM page_list WHERE status = 1 ORDER BY id');
    $pozycjeMenu = $stmtMenu->fetchAll();

    if ($kodHttp === 200) {
        $rekordStrony = PokazPodstrone($pdo, $aktywnyAlias);

        if ($rekordStrony === null) {
            $kodHttp = 404;
            $aktywnyAlias = '';
            $tytulWidoku = '404 Nie znaleziono podstrony';
            $komunikatBledu = 'Błąd 404: Podana podstrona nie istnieje lub nie została opublikowana.';
        } else {
            $tytulWidoku = $rekordStrony['page_title'];
            $trescWidoku = $rekordStrony['page_content'];
        }
    }
} catch (Throwable $e) {
    $kodHttp = 500;
    $pozycjeMenu = [];
    $aktywnyAlias = '';
    $tytulWidoku = '500 Błąd serwera';
    $komunikatBledu = 'Błąd 500: Chwilowy problem z połączeniem z bazą danych. Spróbuj ponownie później.';

    $katalogLogow = __DIR__ . '/logs';
    if (!is_dir($katalogLogow)) {
        mkdir($katalogLogow, 0777, true);
    }
    $wpisLogu = '[' . date('Y-m-d H:i:s') . '] Awaria bazy: ' . $e->getMessage() . PHP_EOL;
    file_put_contents($katalogLogow . '/error.log', $wpisLogu, FILE_APPEND);
}

http_response_code($kodHttp);
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
        </div>
    </header>

    <nav>
        <ul class="ul-nav">
            <?php foreach ($pozycjeMenu as $pozycja) { ?>
                <li>
                    <h2>
                        <a href="index.php?idp=<?php echo htmlspecialchars($pozycja['alias'], ENT_QUOTES, 'UTF-8'); ?>"<?php if ($aktywnyAlias === $pozycja['alias'] and $kodHttp === 200) echo ' aria-current="page"'; ?>>
                            <?php echo htmlspecialchars($pozycja['page_title'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </h2>
                </li>
            <?php } ?>
            <li>
                <h2><a href="lab4.php">Katalog PHP</a></h2>
            </li>
            <li>
                <h2><a href="admin/admin.php">Panel CMS</a></h2>
            </li>
        </ul>
    </nav>

    <main>
        <?php if ($kodHttp !== 200) { ?>
            <section class="karta" role="alert">
                <h2><?php echo htmlspecialchars($tytulWidoku, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($komunikatBledu, ENT_QUOTES, 'UTF-8'); ?></p>
                <p><a href="index.php">Powrót do strony głównej</a></p>
            </section>
        <?php } else { ?>
            <?php echo $trescWidoku; ?>
        <?php } ?>
    </main>

    <footer>
        <hr>
        <p>Identyfikator projektu: <strong><?php echo htmlspecialchars($idProjektu, ENT_QUOTES, 'UTF-8'); ?></strong> | Wersja: <strong><?php echo htmlspecialchars($wersjaProjektu, ENT_QUOTES, 'UTF-8'); ?></strong></p>
    </footer>

</body>
</html>
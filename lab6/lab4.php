<?php
// Pseudokod:
// 1. Dla GET: sprawdz czy podano kategorie (wszystkie, ksiazki, akcesoria). Jesli zla lub tablica -> pokaz blad i pelna liste.
// 2. Dla POST: sprawdz czy produkt i ilosc to liczby calkowite (FILTER_VALIDATE_INT).
// 3. Sprawdz czy produkt istnieje w tablicy, czy ilosc jest z przedzialu 1-5 i czy nie przekracza stanu.
// 4. Gdy OK -> zapisz do $_SESSION['ostatni_wybor'], przekieruj kodem 303 do lab4.php i zakoncz (exit).
// 5. Gdy blad -> ustaw kod HTTP 400, pokaz komunikat bledu przy polu i nie zmieniaj sesji.

session_start();

require_once __DIR__ . '/includes/produkty.php';

$komunikatFiltra = '';
$bladProduktu = '';
$bladIlosci = '';
$bladOgolnyPost = '';

$wybranaKategoria = 'wszystkie';
$dozwoloneKategorie = ['wszystkie', 'ksiazki', 'akcesoria'];

if (isset($_GET['kategoria'])) {
    if (!is_string($_GET['kategoria']) or !in_array($_GET['kategoria'], $dozwoloneKategorie, true)) {
        $komunikatFiltra = 'Niepoprawna wartość filtra kategorii. Wyświetlono pełną listę produktów.';
        $wybranaKategoria = 'wszystkie';
    } else {
        $wybranaKategoria = $_GET['kategoria'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $surowyProdukt = isset($_POST['produkt']) ? $_POST['produkt'] : null;
    $surowaIlosc = isset($_POST['ilosc']) ? $_POST['ilosc'] : null;

    $idProduktu = false;
    $iloscSztuk = false;

    if (!is_string($surowyProdukt) or trim($surowyProdukt) === '') {
        $bladProduktu = 'Wybierz poprawny produkt z listy.';
    } else {
        $idProduktu = filter_var($surowyProdukt, FILTER_VALIDATE_INT);
        if ($idProduktu === false) {
            $bladProduktu = 'Identyfikator produktu musi być liczbą całkowitą.';
        }
    }

    if (!is_string($surowaIlosc) or trim($surowaIlosc) === '') {
        $bladIlosci = 'Podaj ilość sztuk.';
    } else {
        $iloscSztuk = filter_var($surowaIlosc, FILTER_VALIDATE_INT);
        if ($iloscSztuk === false) {
            $bladIlosci = 'Ilość musi być liczbą całkowitą (od 1 do 5).';
        } elseif ($iloscSztuk < 1 or $iloscSztuk > 5) {
            $bladIlosci = 'Ilość musi mieścić się w przedziale od 1 do 5.';
        }
    }

    $znalezionyProdukt = null;
    if ($idProduktu !== false) {
        foreach ($produkty as $rekord) {
            if ($rekord['id'] === $idProduktu) {
                $znalezionyProdukt = $rekord;
                break;
            }
        }
        if ($znalezionyProdukt === null) {
            $bladProduktu = 'Wybrany produkt nie istnieje w katalogu.';
        }
    }

    if ($znalezionyProdukt !== null && $iloscSztuk !== false && $bladIlosci === '') {
        if ($iloscSztuk > $znalezionyProdukt['stan']) {
            $bladIlosci = 'Wybrana ilość (' . $iloscSztuk . ') przekracza stan magazynowy (' . $znalezionyProdukt['stan'] . ' szt.).';
        }
    }

    if ($bladProduktu === '' && $bladIlosci === '' && $znalezionyProdukt !== null) {
        $_SESSION['ostatni_wybor'] = [
            'id' => $znalezionyProdukt['id'],
            'ilosc' => $iloscSztuk
        ];
        header('Location: lab4.php', true, 303);
        exit;
    } else {
        http_response_code(400);
        $bladOgolnyPost = 'Błąd w formularzu (kod 400). Poprzedni wybór został zachowany.';
    }
}

$zapisanyTekstWyboru = 'Brak zapisanego wyboru.';
if (isset($_SESSION['ostatni_wybor']) && is_array($_SESSION['ostatni_wybor'])) {
    $zapisaneId = $_SESSION['ostatni_wybor']['id'];
    $zapisanaIlosc = $_SESSION['ostatni_wybor']['ilosc'];

    foreach ($produkty as $rekord) {
        if ($rekord['id'] === $zapisaneId) {
            $bezpiecznaNazwa = htmlspecialchars($rekord['nazwa'], ENT_QUOTES, 'UTF-8');
            $bezpiecznaIlosc = htmlspecialchars($zapisanaIlosc, ENT_QUOTES, 'UTF-8');
            $zapisanyTekstWyboru = 'Ostatni wybrany produkt: ' . $bezpiecznaNazwa . ', ilość: ' . $bezpiecznaIlosc . ' szt.';
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog PHP - Laboratorium 4</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js" defer></script>
</head>
<body>

    <?php include __DIR__ . '/includes/naglowek.php'; ?>

    <main>
        <section class="karta" style="margin-bottom: 20px;">
            <h2>Zapisany wybór z sesji</h2>
            <p role="status"><?php echo $zapisanyTekstWyboru; ?></p>
        </section>

        <section class="karta" style="margin-bottom: 20px;">
            <h2>Filtruj katalog (GET)</h2>
            <?php if ($komunikatFiltra !== '') { ?>
                <p role="alert" style="color: aqua;">
                    <?php echo htmlspecialchars($komunikatFiltra, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php } ?>

            <form method="get" action="lab4.php">
                <label for="filtr-kategoria">Kategoria produktów:</label>
                <select name="kategoria" id="filtr-kategoria">
                    <option value="wszystkie" <?php if ($wybranaKategoria === 'wszystkie') echo 'selected'; ?>>Wszystkie</option>
                    <option value="ksiazki" <?php if ($wybranaKategoria === 'ksiazki') echo 'selected'; ?>>Książki</option>
                    <option value="akcesoria" <?php if ($wybranaKategoria === 'akcesoria') echo 'selected'; ?>>Akcesoria</option>
                </select>
                <button type="submit">Filtruj</button>
            </form>
        </section>

        <section style="margin-bottom: 20px;">
            <h2>Produkty w katalogu</h2>
            <div class="katalog">
                <?php
                $i = 0;
                $ile = count($produkty);

                while ($i < $ile) {
                    $prod = $produkty[$i];
                    $i++;

                    if ($wybranaKategoria !== 'wszystkie' && $prod['kategoria'] !== $wybranaKategoria) {
                        continue;
                    }

                    switch ($prod['kategoria']) {
                        case 'ksiazki':
                            $etykietaKategorii = 'Książki';
                            break;
                        case 'akcesoria':
                            $etykietaKategorii = 'Akcesoria';
                            break;
                        default:
                            $etykietaKategorii = 'Inne';
                            break;
                    }

                    if ($prod['stan'] === 0) {
                        $komunikatStanu = 'Niedostępny';
                    } elseif ($prod['stan'] >= 1 && $prod['stan'] <= 5) {
                        $komunikatStanu = 'Niski stan';
                    } else {
                        $komunikatStanu = 'Dostępny';
                    }
                ?>
                    <article class="karta">
                        <h3><?php echo htmlspecialchars($prod['nazwa'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p>ID: <?php echo $prod['id']; ?></p>
                        <p>Kategoria: <?php echo htmlspecialchars($etykietaKategorii, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p>Stan: <?php echo $prod['stan']; ?> szt. (<strong><?php echo $komunikatStanu; ?></strong>)</p>
                    </article>
                <?php } ?>
            </div>
        </section>

        <section class="karta" style="margin-bottom: 30px;">
            <h2>Wybierz produkt (POST)</h2>

            <?php if ($bladOgolnyPost !== '') { ?>
                <p role="alert" style="color: #ff6b6b;">
                    <strong><?php echo htmlspecialchars($bladOgolnyPost, ENT_QUOTES, 'UTF-8'); ?></strong>
                </p>
            <?php } ?>

            <form method="post" action="lab4.php">
                <label for="wybor-produkt">Produkt:</label>
                <select name="produkt" id="wybor-produkt">
                    <?php foreach ($produkty as $prodOpcja) { ?>
                        <option value="<?php echo $prodOpcja['id']; ?>">
                            <?php echo htmlspecialchars($prodOpcja['nazwa'], ENT_QUOTES, 'UTF-8'); ?> (stan: <?php echo $prodOpcja['stan']; ?>)
                        </option>
                    <?php } ?>
                </select>
                <?php if ($bladProduktu !== '') { ?>
                    <span role="alert" style="color: #ff6b6b;">
                        <?php echo htmlspecialchars($bladProduktu, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                <?php } ?>

                <label for="wybor-ilosc">Ilość (1-5):</label>
                <select name="ilosc" id="wybor-ilosc">
                    <?php for ($j = 1; $j <= 5; $j++) { ?>
                        <option value="<?php echo $j; ?>"><?php echo $j; ?></option>
                    <?php } ?>
                </select>
                <?php if ($bladIlosci !== '') { ?>
                    <span role="alert" style="color: #ff6b6b;">
                        <?php echo htmlspecialchars($bladIlosci, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                <?php } ?>

                <button type="submit">Zapisz wybór</button>
            </form>
        </section>
    </main>

</body>
</html>
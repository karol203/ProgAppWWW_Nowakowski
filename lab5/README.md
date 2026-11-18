# Sklep z akcesoriami komputerowymi - Laboratorium 5 (PHP: szablon i dynamiczne podstrony)

## 1. Instrukcja uruchomienia i punkt odniesienia
1. Uruchom serwer **Apache** w panelu sterowania **XAMPP**.
2. Umieść katalog projektu w `htdocs/lab5` (np. `C:\xampp\htdocs\lab5`).
3. Punkt odniesienia przed migracją (`lab4` skopiowane do `lab5`): zweryfikowano poprawne działanie filtra katalogu GET, zapisu wyboru do sesji przez POST (303 -> 200) oraz żądania AJAX do `status.php` (odpowiedź `PHP działa`, kod 200).
4. Po migracji główne wejścia aplikacji to:
   * Szablon i podstrony informacyjne: `http://localhost/lab5/index.php`
   * Katalog produktów i formularz sesji: `http://localhost/lab5/lab4.php`
   * Endpoint AJAX: `http://localhost/lab5/status.php`

## 2. Dostępne adresy podstron (Mapa widoków)
* `http://localhost/lab5/index.php` lub `http://localhost/lab5/index.php?idp=glowna` – Strona Główna (`html/glowna.html`)
* `http://localhost/lab5/index.php?idp=produkt` – Produkt (`html/produkt.html`)
* `http://localhost/lab5/index.php?idp=kontakt` – Kontakt (`html/kontakt.html`)
* `http://localhost/lab5/index.php?idp=filmy` – Filmy (`html/filmy.html`)

## 3. Pseudokod wyboru widoku
1. Zdefiniuj mapę podstron `$strony` przypisującą bezpiecznemu kluczowi tytuł oraz stałą ścieżkę do fragmentu w `html/`.
2. Odczytaj parametr `$_GET['idp']`:
   * Jeśli parametr nie istnieje lub jest pustym ciągiem `""` -> wybierz klucz domyślny `'glowna'`.
   * Jeśli parametr nie jest tekstem (`!is_string`, np. przekazano tablicę `idp[]`) lub klucza nie ma w tablicy `$strony` -> ustaw kod odpowiedzi `http_response_code(404)` i przygotuj komunikat błędu 404 z linkiem powrotnym.
3. Dla poprawnego klucza zbuduj ścieżkę pliku względem `__DIR__` i wartości z mapy (bez doklejania surowych danych z `$_GET`).
4. Sprawdź dostępność pliku przez `is_file()` oraz `is_readable()`:
   * Jeśli plik nie istnieje lub nie można go odczytać -> ustaw `http_response_code(500)`, nie wywołuj `include` i wyświetl komunikat błędu serwera bez ujawniania ścieżki systemowej.
   * Jeśli plik jest poprawny -> zachowaj kod `200`, wygeneruj wspólny szablon HTML (oznaczając aktywny link w menu przez `aria-current="page"`) i dołącz fragment wewnątrz `<main>` za pomocą `include`.

## 4. Wyniki prób testowych

| Próba | Kroki testowe | Wynik oczekiwany | Wynik rzeczywisty |
| :--- | :--- | :--- | :--- |
| **Adres domyślny i menu** | Otwarcie `index.php`, `index.php?idp=` oraz klikanie kolejnych linków w menu nawigacyjnym. | Oba adresy domyślne pokazują stronę główną. Linki wczytują odpowiednie fragmenty ze wspólnym szablonem i tytułem. | Zgodnie z oczekiwaniami. Aktywna pozycja otrzymuje `aria-current="page"`, w Network status 200. |
| **Nieznany lub tablicowy klucz** | Wejście na `?idp=nieznana` oraz `?idp[]=filmy`. | Kod HTTP 404 Not Found, komunikat błędu w szablonie i link powrotny, brak ostrzeżeń PHP. | Zgodnie z oczekiwaniami. W panelu Network widoczny status 404, brak błędów i ostrzeżeń interpretera PHP. |
| **Próba podania ścieżki** | Wejście na adres `?idp=..%2Fstatus.php`. | Kod HTTP 404. Zawartość `status.php` nie zostaje dołączona (ochrona przez białą listę mapy). | Zgodnie z oczekiwaniami. Zwrócono kod 404, plik spoza mapy nie został wczytany. |
| **Brak pliku widoku** | Tymczasowa zmiana nazwy `html/filmy.html`, wejście na `?idp=filmy`, a następnie przywrócenie pliku. | Kod HTTP 500 Internal Server Error, komunikat dla użytkownika bez ścieżki systemowej. | Zgodnie z oczekiwaniami. `is_file()` wykrywa brak pliku, zwraca HTTP 500 i ukrywa ścieżkę dyskową. |
| **Strona główna i AJAX** | Sprawdzenie stylów, obrazów, animacji jQuery A/B/C oraz kliknięcie „Sprawdź serwer” na `index.php`. | Działają style, grafiki i animacje. Żądanie AJAX do `status.php` zwraca kod 200 i tekst „PHP działa”. | Zgodnie z oczekiwaniami. Wszystkie skrypty i ścieżki zasobów działają z poziomu `index.php`. |
| **Katalog i sesja po migracji** | Przejście z menu do `lab4.php`, filtr GET, zapis POST, powrót na `index.php`, wejście w Filmy i powrót do `lab4.php`. | Zapis POST daje 303 -> 200. Po nawigacji między podstronami `index.php` wybór w sesji nadal jest widoczny. | Zgodnie z oczekiwaniami. Sesja PHP utrzymuje stan pomiędzy `index.php` a `lab4.php`. |
| **Filmy i układ mobilny** | Otwarcie `index.php?idp=filmy`, uruchomienie 3 filmów, nawigacja klawiszem Tab i test szerokości 360 px. | Trzy filmy działają, linki są dostępne z klawiatury, a przy 360 px strona nie przewija się w poziomie. | Zgodnie z oczekiwaniami. Ramki `iframe` skalują się responsywnie dzięki `width: 100%` i `aspect-ratio`. |

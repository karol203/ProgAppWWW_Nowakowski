# Sklep z akcesoriami komputerowymi - Laboratorium 6 Część 1 (PHP i baza danych w prostym CMS)

## 1. Instrukcja uruchomienia i odtworzenia środowiska
1. Uruchom usługi **Apache** oraz **MySQL** w panelu sterowania **XAMPP**.
2. Umieść katalog projektu w `htdocs/lab6` (np. `C:\xampp\htdocs\lab6`).
3. Otwórz `http://localhost/phpmyadmin/`, przejdź do zakładki **Import** (lub **SQL**) i wykonaj kolejno:
   * `sql/schema.sql` – tworzy bazę `moja_strona` oraz tabelę `page_list` z kodowaniem `utf8mb4` i unikalnym indeksem na kolumnie `alias`.
   * `sql/seed.sql` – wstawia 4 opublikowane podstrony (`status = 1`: `glowna`, `produkt`, `kontakt`, `filmy`) oraz 1 szkic (`status = 0`: `regulamin`).
4. W phpMyAdmin utwórz lokalnego użytkownika bazy (np. `cms_reader`@`localhost` z własnym hasłem) i nadaj mu wyłącznie uprawnienie **SELECT** do tabeli `moja_strona.page_list`.
5. Skopiuj plik `cfg.example.php` jako `cfg.php` i uzupełnij dane dostępowe utworzonego użytkownika (plik `cfg.php` oraz katalog `logs/` są wykluczone w `.gitignore`).
6. Wejścia aplikacji (wersja projektu `v1.5`):
   * Strona główna i podstrony CMS: `http://localhost/lab6/index.php`
   * Katalog produktów (sesja/GET/POST): `http://localhost/lab6/lab4.php`
   * Szablon panelu administracyjnego: `http://localhost/lab6/admin/admin.php`
   * Endpoint AJAX: `http://localhost/lab6/status.php`

## 2. Pseudokod odczytu strony z bazy danych
1. Odczytaj parametr `$_GET['idp']`:
   * Brak parametru lub pusty ciąg `""` -> przyjmij domyślny alias `'glowna'`.
   * Brak ciągu tekstowego (`!is_string`, np. tablica `idp[]`) lub niezgodność z formatem `^[a-z0-9-]{1,20}$` -> ustaw kod HTTP `404` bez odpytywania bazy.
2. W bloku `try` wczytaj `cfg.php` i `showpage.php`, a następnie pobierz opublikowane pozycje menu (`SELECT alias, page_title FROM page_list WHERE status = 1 ORDER BY id`).
3. Jeśli alias ma poprawny format, wywołaj funkcję `PokazPodstrone($pdo, $alias)` korzystającą z `prepare()` i `execute()` (`WHERE alias = :alias AND status = 1 LIMIT 1`):
   * Gdy zwróci `null` (brak rekordu lub szkic `status = 0`) -> ustaw kod HTTP `404` i wyświetl komunikat błędu z linkiem powrotnym.
   * Gdy zwróci rekord -> zachowaj kod `200`, oznacz aktywny link atrybutem `aria-current="page"` i wypisz pobrany fragment HTML wewnątrz `<main>`.
4. W bloku `catch (Throwable $e)` zapisz szczegóły techniczne do lokalnego pliku `logs/error.log`, ustaw kod HTTP `500` i pokaż użytkownikowi ogólny komunikat awarii bez zdradzania danych dostępu, SQL ani ścieżek systemowych.

## 3. Wyniki prób testowych

| Próba | Kroki testowe | Wynik oczekiwany | Wynik rzeczywisty |
| :--- | :--- | :--- | :--- |
| **Odczyt i aktualizacja treści** | Otwarcie `index.php` oraz `index.php?idp=`, a następnie edycja treści rekordu `glowna` w phpMyAdmin i odświeżenie strony. | Oba adresy otwierają `glowna` (HTTP 200). Zmiana w bazie jest widoczna od razu bez edycji plików PHP i HTML. | Zgodnie z oczekiwaniami. W panelu Network status 200, edycja w kolumnie `page_content` odświeżyła się natychmiast na stronie. |
| **Nowa strona i menu** | Dodanie w phpMyAdmin nowego opublikowanego rekordu (`status = 1`) z unikalnym aliasem. | Link automatycznie pojawia się w menu, otwiera podstronę bez zmiany kodu PHP i ma `aria-current="page"`. | Zgodnie z oczekiwaniami. Nowa pozycja wygenerowała się dynamicznie z bazy danych i otrzymała atrybut `aria-current="page"`. |
| **Szkic i publikacja** | Zmiana `status` rekordu na `0`, próba wejścia przez `?idp=...`, ponowna zmiana na `1` oraz próba dodania duplikatu aliasu. | Szkic znika z menu i daje HTTP 404. Po zmianie na `1` wraca. Duplikat aliasu odrzuca ograniczenie `UNIQUE`. | Zgodnie z oczekiwaniami. Dla `status = 0` zwrócono HTTP 404; próba zapisu powtórzonego aliasu została zablokowana błędem `#1062 Duplicate entry`. |
| **Błędne dane w adresie** | Wywołanie adresów: `?idp=nieznana`, `?idp[]=filmy`, `?idp=../cfg.php` oraz `?idp=' OR 1=1--`. | Każda próba zwraca kod HTTP 404 bez ostrzeżeń PHP ani wycieku danych. | Zgodnie z oczekiwaniami. Walidacja formatu aliasu (`^[a-z0-9-]{1,20}$`) i zapytania parametryzowane PDO odrzuciły wszystkie próby kodem 404. |
| **Awaria bazy** | Tymczasowe wpisanie błędnego portu (np. `3307`) w `cfg.php` i odświeżenie `index.php`. | Strona zwraca HTTP 500 bez szczegółów technicznych, a przyczynę zapisuje lokalny log. | Zgodnie z oczekiwaniami. W Network status 500, ogólny komunikat w szablonie, a szczegóły wyjątku zapisane w `logs/error.log`. |
| **Funkcje po migracji** | Sprawdzenie stylów, obrazów, animacji jQuery, AJAX (`status.php`), 3 filmów oraz katalogu `lab4.php` z sesją. | Działają style, obrazy, animacje, AJAX, filmy oraz filtr i sesja w `lab4.php` (POST 303 -> GET 200). | Zgodnie z oczekiwaniami. Wszystkie zasoby statyczne, filmy oraz mechanizm sesji między podstronami działają poprawnie. |
| **Odtworzenie środowiska** | Utworzenie czystej bazy testowej, import `sql/schema.sql` i `sql/seed.sql`, uzupełnienie `cfg.php` z `cfg.example.php`. | Projekt uruchamia się zgodnie z instrukcją, poprawnie wyświetlając polskie znaki (`utf8mb4`). | Zgodnie z oczekiwaniami. Skrypty SQL odtworzyły strukturę i dane wraz z poprawnym kodowaniem polskich znaków. |

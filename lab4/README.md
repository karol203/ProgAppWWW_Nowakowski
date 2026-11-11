# Sklep z akcesoriami komputerowymi - Laboratorium 4 (PHP formularze i sesja)

## 1. Instrukcja uruchomienia
1. Uruchom serwer **Apache** w panelu sterowania **XAMPP**.
2. Umieść katalog projektu w `htdocs/lab4` (np. `C:\xampp\htdocs\lab4`).
3. Otwórz stronę główną pod adresem: `http://localhost/lab4/` lub bezpośrednio katalog PHP: `http://localhost/lab4/lab4.php`.

## 2. Pseudokod obsługi formularza (poprawne i błędne żądania)
* **Żądanie GET (Filtrowanie):**
  1. Odczytaj parametr `$_GET['kategoria']`.
  2. Jeśli parametr nie istnieje -> ustaw filtr na `wszystkie`.
  3. Jeśli parametr nie jest tekstem (np. przekazano tablicę `kategoria[]`) lub nie należy do listy (`wszystkie`, `ksiazki`, `akcesoria`) -> ustaw komunikat ostrzegawczy i wyświetl wszystkie produkty. Nie modyfikuj danych w `$_SESSION`.
* **Żądanie POST (Zapis wyboru do sesji):**
  1. Sprawdź, czy metoda żądania to `POST`.
  2. Sprawdź, czy pola `produkt` oraz `ilosc` istnieją w `$_POST` i są typu tekstowego (`is_string`).
  3. Zweryfikuj oba pola przez `filter_var(..., FILTER_VALIDATE_INT)`.
  4. Odszukaj produkt o podanym `id` w tablicy `$produkty`. Sprawdź, czy ilość mieści się w zakresie `1..5` oraz czy nie przekracza pola `stan` wybranego produktu.
  5. **Ścieżka poprawna:** Zapisz `id` oraz `ilosc` w `$_SESSION['ostatni_wybor']`, wyślij nagłówek `Location: lab4.php` z kodem `303 See Other` i zakończ wykonywanie skryptu (`exit`).
  6. **Ścieżka błędna:** Ustaw kod odpowiedzi `http_response_code(400)`, przypisz komunikat błędu do odpowiedniego pola formularza i wygeneruj widok bez nadpisywania wcześniejszego wyboru w sesji.

## 3. Wyniki prób testowych

| Próba | Kroki testowe | Wynik oczekiwany | Wynik rzeczywisty |
| :--- | :--- | :--- | :--- |
| **Filtr GET** | Wybór `?kategoria=ksiazki`, a następnie wejście na `?kategoria=nieznana` oraz `?kategoria[]=ksiazki`. | Dla `ksiazki` widoczne tylko książki. Dla nieznanej kategorii lub tablicy widoczna pełna lista i komunikat. | Zgodnie z oczekiwaniami. Metoda GET zwraca kod 200, a błędny parametr wyświetla komunikat i pełną listę. |
| **Poprawny POST i odświeżenie** | Wybór dostępnego produktu (np. ID 1, stan 8) i ilości 2, zatwierdzenie formularza i odświeżenie strony (F5). | W Network widoczny POST 303, a po nim GET 200. Po odświeżeniu i zmianie filtra wybór pozostaje widoczny. | Zgodnie z oczekiwaniami. Zastosowano wzorzec PRG (Post/Redirect/Get z kodem 303), odświeżenie nie ponawia wysyłki POST. |
| **Błędne wartości POST** | Zmiana w Inspektorze wartości ilości na `0`, `6`, `2.5`, `2abc`, usunięcie `name`, zmiana na `ilosc[]` oraz nieznane ID produktu. | Każda próba zwraca kod HTTP 400 Bad Request, wyświetla komunikat błędu i zachowuje poprzedni wybór z sesji. | Zgodnie z oczekiwaniami. W zakładce Network widoczny status 400, a sesja przechowuje wcześniejszy poprawny wybór. |
| **Stan magazynowy** | Wybór produktu ze stanem `1` (ID 2) oraz ilości `2` sztuki. | Serwer odrzuca wybór (HTTP 400), mimo że liczba 2 mieści się w przedziale 1–5. | Zgodnie z oczekiwaniami. Wyświetlono komunikat o przekroczeniu stanu magazynowego i zwrócono kod 400. |
| **Odrębna sesja i tekst HTML** | Otwarcie strony w oknie prywatnym; tymczasowa zmiana nazwy produktu w `produkty.php` na `<b>Test</b>`. | W oknie prywatnym brak wyboru. Znacznik `<b>Test</b>` wyświetla się jako zwykły tekst, bez pogrubienia. | Zgodnie z oczekiwaniami. Funkcja `htmlspecialchars()` neutralizuje znaczniki HTML. |
| **Strona po zmianie ścieżek** | Otwarcie `index.html`, podstron w `html/`, sprawdzenie animacji z lab3 i przycisku `status.php`. | Wszystkie style, obrazy, linki, animacje jQuery i zapytanie AJAX działają poprawnie. | Zgodnie z oczekiwaniami. Ścieżki względne po uporządkowaniu katalogów działają prawidłowo. |

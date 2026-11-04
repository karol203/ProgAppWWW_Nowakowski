# Sklep z akcesoriami komputerowymi - Laboratorium 3

1. Instrukcja uruchomienia i środowisko
-Zainstaluj pakiet XAMPP i uruchom serwer Apache w panelu sterowania.
-Skopiuj katalog lab3 do folderu htdocs w katalogu instalacyjnym XAMPP.
-Uruchom projekt w przeglądarce pod adresem: `http://localhost/lab3/`.
-Test samego skryptu PHP dostępny jest pod adresem: `http://localhost/lab3/status.php.

Wersja biblioteki jQuery: 4.0.0

2. Pseudokody funkcji:
-Animacja A: Po kliknięciu odwróć wartość zmiennej logicznej stanu. Ustaw atrybut `aria-pressed` zgodnie ze stanem. Wywołaj `.stop(true, false)` w celu wyczyszczenia kolejki i płynnej zmiany kierunku. Animuj wymiary do 240x130 px lub 160x90 px w czasie 300 ms.
-Animacja B: Zapisuj w dwóch zmiennych logicznych stan kursora oraz fokusu. Po każdej zmianie wywołaj funkcję sprawdzającą: jeśli przynajmniej jeden stan jest prawdziwy, zatrzymaj kolejkę `.stop(true, false)` i animuj do 220x120 px. Jeśli oba stany są fałszywe, animuj do bazowego 160x90 px.
-Animacja C: Po kliknięciu sprawdź, czy obiekt jest w trakcie animacji lub czy licznik kroków wynosi 5. Jeśli tak – przerwij działanie. W przeciwnym razie zwiększ licznik o 1, zaktualizuj tekst w elemencie `role="status"`, wylicz nowe wymiary ze wzoru (szerokość: `160 + kroki * 20`, wysokość: `90 + kroki * 10`) i uruchom `.animate()`.
-Reset A i C: Po kliknięciu zatrzymaj animacje obiektów A i C, przywróć im natychmiast wymiary 160x90 px przez `.css()`, wyzeruj zmienne stanów i licznik kroków oraz ustaw `aria-pressed="false"`.
-Żądanie AJAX: Po kliknięciu zablokuj przycisk i wyświetl napis „Ładowanie...”. Wyślij żądanie `$.ajax()` metodą GET do `status.php`. W `.done()` wstaw odpowiedź przez `.text()`, w `.fail()` pokaż komunikat błędu, a w `.always()` odblokuj przycisk.

3. Wyniki testów:

|Próba | Kroki testowe | Wynik oczekiwany | Wynik rzeczywisty |
| :--- | :--- | :--- | :--- |
| Szybkie kliknięcia A | Reset, a następnie wykonanie 10 szybkich kliknięć na obiekcie A. | Stan końcowy odpowiada parzystej liczbie kliknięć (rozmiar bazowy 160x90 px); brak zaległej kolejki animacji. | Zgodnie z oczekiwaniami. Obiekt zatrzymuje się w stanie bazowym, `.stop(true, false)` usuwa kolejkę. |
| Kursor i fokus B | Najazd kursorem, kliknięcie (nadanie fokusu), zjechanie kursorem, a następnie utrata fokusu (Tab/kliknięcie obok). | Wyjście kursora przy zachowanym fokusie nie zmniejsza obiektu. Dopiero utrata obu stanów przywraca 160x90 px. | Zgodnie z oczekiwaniami. Obiekt wraca do 160x90 px dopiero po wywołaniu zarówno `mouseleave`, jak i `blur`. |
| Limit i reset C | Wykonanie ponad 5 kliknięć (w tym kliknięcia w trakcie trwania animacji) oraz kliknięcie Reset podczas ruchu. | Kliknięcia w trakcie ruchu są pomijane; po 5 krokach obiekt osiąga 260x140 px i nie rośnie dalej. Reset natychmiast zeruje stan. | Zgodnie z oczekiwaniami. `.is(':animated')` blokuje kliknięcia w ruchu, a reset w trakcie przejścia od razu ustawia 160x90 px. |
| Klawiatura i ruch | Nawigacja klawiszem Tab, aktywacja przez Enter/Spację, włączenie `prefers-reduced-motion: reduce` i odświeżenie. | Kontrolki mają widoczny fokus; ograniczenie ruchu ustawia `$.fx.off = true` i zmiany wymiarów są natychmiastowe. | Zgodnie z oczekiwaniami. Zmiana rozmiaru przy włączonej preferencji odbywa się natychmiastowo bez animacji pośredniej. |
| AJAX: Kod 200 (GET) | Kliknięcie „Sprawdź serwer” przy obecnym pliku `status.php` i podgląd w zakładce Network. | Metoda GET, status 200 OK, wyświetlenie tekstu „PHP działa” bez przeładowania strony. | Zgodnie z oczekiwaniami. Odebrano odpowiedź `PHP działa` (kod 200). |
| AJAX: Błąd 404 | Tymczasowa zmiana nazwy `status.php`, kliknięcie przycisku, a następnie przywrócenie nazwy i ponowna próba. | Status 404 w Network, komunikat błędu z `.fail()`, odblokowanie przycisku w `.always()`, sukces po przywróceniu pliku. | Zgodnie z oczekiwaniami. Pojawił się komunikat błędu (404), a po przywróceniu pliku ponowna próba zwróciła `PHP działa`. |

function initTheme() {
    let themeBtn = document.getElementById('theme-toggle');
    if (!themeBtn) return;

    let htmlElement = document.documentElement;
    let currentTheme = localStorage.getItem('theme');

    if (currentTheme === null) {
        currentTheme = 'dark';
    }
    function applyTheme(theme) {
        htmlElement.setAttribute('data-theme', theme);
        
        if (theme === 'light') {
            themeBtn.setAttribute('aria-pressed', 'true');
            themeBtn.textContent = 'Tryb ciemny';
        } else {
            themeBtn.setAttribute('aria-pressed', 'false');
            themeBtn.textContent = 'Tryb jasny';
        }
    }

    applyTheme(currentTheme);

    themeBtn.addEventListener('click', function() {
        if (currentTheme === 'dark') {
            currentTheme = 'light';
        } else {
            currentTheme = 'dark';
        }
        applyTheme(currentTheme);
        localStorage.setItem('theme', currentTheme);
    });
}
initTheme();

function initClock() {
    let clockDisplay = document.getElementById('clock-display');
    let startBtn = document.getElementById('start-btn');
    let stopBtn = document.getElementById('stop-btn');

    if (!clockDisplay || !startBtn || !stopBtn) return;

    let clockInterval = null;

    function updateClock() {
        let now = new Date();
        
        let year = now.getFullYear();
        let month = now.getMonth() + 1;
        let day = now.getDate();
        
        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();

        if (month < 10) { month = '0' + month; }
        if (day < 10) { day = '0' + day; }
        if (hours < 10) { hours = '0' + hours; }
        if (minutes < 10) { minutes = '0' + minutes; }
        if (seconds < 10) { seconds = '0' + seconds; }

        clockDisplay.textContent = day + '.' + month + '.' + year + ', ' + hours + ':' + minutes + ':' + seconds;
    }

    updateClock();

    function startClock() {
        if (clockInterval === null) {
            clockInterval = setInterval(updateClock, 1000);
        }
    }

    function stopClock() {
        if (clockInterval !== null) {
            clearInterval(clockInterval);
            clockInterval = null;
        }
    }

    startClock();
    startBtn.addEventListener('click', startClock);
    stopBtn.addEventListener('click', stopClock);
}
initClock();

function initSearch() {
    let searchInput = document.getElementById('search-input');
    let searchStatus = document.getElementById('search-status');
    let cards = document.querySelectorAll('.karta');

    if (!searchInput || cards.length === 0) return;

    searchInput.addEventListener('input', function(e) {
        let query = e.target.value.trim().toLowerCase();
        let visibleCount = 0;

        for (let i = 0; i < cards.length; i++) {
            let card = cards[i];
            let titleElement = card.querySelector('h2');
            let title = titleElement.textContent.toLowerCase();

            if (title.indexOf(query) !== -1) {
                card.hidden = false;
                visibleCount++;
            } else {
                card.hidden = true;
            }
        }

        if (visibleCount === 0) {
            searchStatus.textContent = "Brak wyników dla zapytania: '" + query + "'";
        } else {
            searchStatus.textContent = "Znaleziono produktów: " + visibleCount;
        }
    });
}
initSearch();

function initForm() {
    let form = document.getElementById('contact-form');
    if (!form) return; 

    let products = [
        { id: 'p1', nazwa: 'Myszka SteelSeries rival 650 Wireless', cenaGrosze: 54999 },
        { id: 'p2', nazwa: 'Arctis Nova 7 Wireless Gen 2', cenaGrosze: 64999 },
        { id: 'p3', nazwa: 'Apex Pro TKL Gen 3 White - US', cenaGrosze: 105999 }
    ];

    function calculateTotal(price, qty) {
        return price * qty;
    }

    let productSelect = document.getElementById('product-select');
    let quantityInput = document.getElementById('quantity-input');
    let liveCalcDisplay = document.getElementById('live-calculation');
    let messageInput = document.getElementById('message-input');
    let messageError = document.getElementById('message-error');
    let summarySection = document.getElementById('summary-section');

    function updateLiveValue() {
        summarySection.textContent = ''; 
        let selectedId = productSelect.value;
        let qty = Number(quantityInput.value); 
        
        let product = null;
        for (let i = 0; i < products.length; i++) {
            if (products[i].id === selectedId) {
                product = products[i];
                break;
            }
        }

        let isValidQty = false;
        if (Number.isInteger(qty) && qty >= 1 && qty <= 5) {
            isValidQty = true;
        }

        if (product === null || isValidQty === false) {
            liveCalcDisplay.textContent = 'Wprowadź poprawne dane (wybierz produkt i ilość 1-5), aby zobaczyć wycenę.';
            return;
        }

        let totalGrosze = calculateTotal(product.cenaGrosze, qty);
        let totalPLN = totalGrosze / 100;
        
        liveCalcDisplay.textContent = 'Bieżąca wartość: ' + totalPLN.toFixed(2) + ' zł';
    }

    productSelect.addEventListener('change', updateLiveValue);
    quantityInput.addEventListener('input', updateLiveValue);

    messageInput.addEventListener('input', function() {
        messageError.textContent = '';
        summarySection.textContent = '';
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault(); 

        let messageText = messageInput.value.trim();

        if (messageText.length < 10) {
            messageError.textContent = 'Wiadomość musi zawierać minimum 10 znaków.';
            messageInput.focus(); 
            return; 
        }

        let selectedId = productSelect.value;
        let qty = Number(quantityInput.value);
        
        let product = null;
        for (let j = 0; j < products.length; j++) {
            if (products[j].id === selectedId) {
                product = products[j];
                break;
            }
        }
        
        let totalGrosze = calculateTotal(product.cenaGrosze, qty);
        let totalPLN = totalGrosze / 100;

        summarySection.textContent = 
            "=== PODSUMOWANIE ZAMÓWIENIA ===\n" +
            "Wybrany produkt: " + product.nazwa + "\n" +
            "Ilość: " + qty + " szt.\n" +
            "Wartość do zapłaty: " + totalPLN.toFixed(2) + " zł\n" +
            "Twoja wiadomość: " + messageText;
    });
}
initForm();
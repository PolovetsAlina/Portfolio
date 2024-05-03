// Funzione per gestire lo stato della carella
function cartStatus() {
    const cartWrapper = document.querySelector('.cart-wrapper');
    const cartEmpty = document.querySelector('[data-cart-empty]');

    const orderForm = document.querySelector('#order-form');

    // Controlla se la carella contiene almeno un elemento
    if (cartWrapper.children.length > 0) {
        // Nasconde il messaggio di carella vuota
        cartEmpty.classList.add('none');
        orderForm.classList.remove('none');
    } else {
        // Mostra il messaggio di carella vuota
        cartEmpty.classList.remove('none');
        orderForm.classList.add('none');
    }
}

// Codice da eseguire quando il DOM è completamente caricato
document.addEventListener('DOMContentLoaded', function() {
    // Chiamata alla funzione cartStatus per inizializzare lo stato della carella
    cartStatus();
});


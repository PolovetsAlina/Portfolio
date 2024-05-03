// Gestione dei clic sui pulsanti "+" e "-"
window.addEventListener('click', function(event) {
  // Se il clic avviene sui pulsanti "+" o "-"
  if (event.target.dataset.action === 'plus' || event.target.dataset.action === 'minus') {
      // Trova il wrapper del contatore
      const counterWrapper = event.target.closest('.counter-wrapper');
      // Trova l'elemento del contatore
      const counter = counterWrapper.querySelector('[data-counter]');

      // Se il clic avviene sul pulsante "+"
      if (event.target.dataset.action === 'plus') {
          // Aumenta il valore del contatore
          counter.innerText = parseInt(counter.innerText) + 1;
      }
      // Se il clic avviene sul pulsante "-"
      else if (event.target.dataset.action === 'minus') {
          // Controlla se il contatore è maggiore di 1
          if (parseInt(counter.innerText) > 1) {
              // Diminuisci il valore del contatore
              counter.innerText = parseInt(counter.innerText) - 1;
          } else {
              // Rimuovi l'elemento dalla carella
              const cartItem = event.target.closest('.cart-item');
              if (cartItem) {
                  cartItem.remove();
              }
          }
      }
      // Aggiorna lo stato della carella dopo l'aggiunta o la rimozione dell'elemento
      cartStatus();
      calcCartPrice ();

  }

  if(event.target.hasAttribute('data-action') && event.target.closest('.cart-wrapper')){
    calcCartPrice ();
  }
});



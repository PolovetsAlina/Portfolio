function calcCartPrice() {
    const cartWrapper = document.querySelector('.cart-wrapper');
    const priceElements = cartWrapper.querySelectorAll('.price__currency');
    const totalPriceEl = document.querySelector('.total-price');

    let totalPrice = 0;

    if (priceElements.length > 0) {
        priceElements.forEach(function(item) {
            const amountEl = item.closest('.cart-item').querySelector('[data-counter]');
            totalPrice += parseInt(item.innerText) * parseInt(amountEl.innerText);
        });
    }

    totalPriceEl.innerText = totalPrice;
}

document.addEventListener('DOMContentLoaded', function() {
    calcCartPrice();
});

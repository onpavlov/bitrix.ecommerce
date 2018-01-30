// Устанавливаем событие на загрузку страницы
document.addEventListener('DOMContentLoaded', function () {
    var ecommerce = new BxEcommerce();
    ecommerce.parse();

    // Устанавливаем событие на изменение страницы
    document.body.addEventListener("DOMNodeInserted",function(e) {
        clearTimeout(window.ecommerceTimer);
        var containrers = document.querySelectorAll('[data-etype]');

        if (containrers.length > 0) {
            window.ecommerceTimer = setTimeout(function () {
                ecommerce.parse(true);
            }, 1000);
        }
    }, false);
});
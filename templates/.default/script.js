/**
 * @param product
 */
function addProductItem(product) {
    window.bxEcommerce = (window.bxEcommerce || {});
    bxEcommerce[product.id] = product;
}

/**
 * @param orderId
 */
function getTransactionOneClickCode(orderId) {
    orderId = orderId || 0;

    if (orderId === 0) return;

    $.get(
        window.bxEcommerceTemplatePath + '/ajax/getTransactionOneClickCode.php',
        { 'orderId' : orderId },
        function (response) {
            if (response.success) {
                eval(response.code);
            }
        },
        'json'
    );
}
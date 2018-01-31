function addProductItem(product) {
    window.bxEcommerce = (window.bxEcommerce || {});
    bxEcommerce[product.id] = product;
}
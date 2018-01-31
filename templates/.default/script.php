$(document).ready(function() {
    $('body').on('click', '[data-eproduct-event=detail]', function(e) {
        var product = getProduct(this);

        if (product !== false) {
            dataLayer.push({
                "event" : "productClick",
                "ecommerce" : {
                    "click" : {
                        "actionField" : {
                            "list" : "homepage"
                        },
                        "products" : [product]
                    }
                }
            });
        }
    })
        .on('click', '[data-eproduct-event=buy]', function(e) {
            var product = getProduct(this);

            if (product !== false) {
                dataLayer.push({
                    "event" : "addToCart",
                    "ecommerce" : {
                        "currencyCode": "RUB", // @todo сделать подстановку валюты
                        "add" : {
                            "products" : [product]
                        }
                    }
                });
            }
        })
        .on('click', '[data-eproduct-event=remove_cart]', function(e) {
            var product = getProduct(this);

            if (product !== false) {
                dataLayer.push({
                    "event" : "removeFromCart",
                    "ecommerce" : {
                        "currencyCode": "RUB", // @todo сделать подстановку валюты
                        "remove" : {
                            "products" : [product]
                        }
                    }
                });
            }
        })
        .on('click', '[data-eproduct-event=oneclick_buy]', function(e) {
            var container = $('[data-ecommerce-container=oneclick_buy]'),
                products = getProducts(container),
                options = getOptions(container);

            if (products.length > 0) {
                dataLayer.push({
                    "event" : "transactionOneClick",
                    "ecommerce" : {
                        "purchase" : {
                            "actionField" : options,
                            "products" : products
                        }
                    }
                });
            }
        });

    /**
     * @param elem
     * @returns {boolean} | {Object}
     */
    function getProduct(elem) {
        var id = undefined,
            i = 0;

        while (i < 10 && id === undefined) {
            id = $(elem).parent().find('input[name=eproduct_id]').val();
            elem = $(elem).parent();
            i++;
        }

        return (bxEcommerce[id] === undefined) ? false : bxEcommerce[id];
    }

    /**
     *
     * @param container
     * @returns {Array}
     */
    function getProducts(container) {
        var product = {}, products = [], id = 0;

        container.find('input[name=eproduct_id]').each(function (i, e) {
            id = $(this).val();
            product = bxEcommerce[id];

            if (product !== undefined) products.push(product);
        });

        return products;
    }

    /**
     * @param container
     * @returns {{}}
     */
    function getOptions(container) {
        var options = {};

        container.find('[data-etype=option]').each(function (i, e) {
            var name = $(this).attr('name');
            var val = $(this).val();

            if (name.length > 0 && val.length > 0) {
                options[name] = val;
            }
        });

        return options;
    }
});
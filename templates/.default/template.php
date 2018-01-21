<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<script type="text/javascript" id="google_ecommerce">
    document.addEventListener('DOMContentLoaded', function () {
        var containers = document.querySelectorAll('[data-etype]'),
            data = { "ecommerce" : {} },
            impressionsProducts = [], detailProducts = [];

        Array.prototype.forEach.call(containers, function (container) {
            var type = container.getAttribute('data-etype');
console.log(type);
            switch (type) {
                case 'detail':
                    detailProducts.push(getProductData(container));
                    break;

                case 'impressions':
                    impressionsProducts.push(getProductData(container));
                    break;
            }
        });
console.log(impressionsProducts);
        if (impressionsProducts.length > 0) {
            data.ecommerce.impressions = { "products" : impressionsProducts };
        }
console.log(detailProducts);
        if (detailProducts.length > 0) {
            data.ecommerce.detail = { "products" : detailProducts };
        }

        console.log(data);
        document.body.addEventListener("DOMNodeInserted",function(e){ console.log('страница изменена'); },false);
    });

    /**
     * @param container
     * @param position
     * @returns {{position: *|number}}
     */
    function getProductData(container, position) {
        position = position || 0;

        var product = { "position" : position },
            productTpl = {
                "id" : "",
                "name" : "",
                "price" : "",
                "brand" : "",
                "category" : ""
            };

        for (var label in productTpl) {
            if (label.length > 0) {
                product[label] = container.querySelector('[data-eproduct=' + label + ']').value;
            }
        }

        return product;
    }
</script>
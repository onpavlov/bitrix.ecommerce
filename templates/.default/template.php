<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<script type="text/javascript" id="google_ecommerce">
    var bxEcommerce = {
        /**
         * @param containers
         */
        "parse" : function (containers) {
            containers = containers || document.querySelectorAll('[data-etype]');
                var data = { "ecommerce" : { "detail" : [], "impressions" : [] } },
                    impressionsProducts = data.ecommerce.impressions,
                    detailProducts = data.ecommerce.detail,
                    product = {};

            Array.prototype.forEach.call(containers, function (container) {
                var type = container.getAttribute('data-etype');
                console.log(type);
                switch (type) {
                    case 'detail':
                            product = this.getProductData(container);

                        if (!this.hasProduct(detailProducts, product)) {
                            detailProducts.push();
                        }
                        break;

                    case 'impressions':
                        var position = impressionsProducts[impressionsProducts.length - 1].position,
                            product = this.getProductData(container, position + 1);

                        if (!this.hasProduct(impressionsProducts, product)) {
                            impressionsProducts.push(product);
                        }
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
        },

        /**
         * @param container
         * @param position
         * @returns {{position: *|number}}
         */
        "getProductData" : function (container, position) {
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
        },

        /**
         * @param arr
         * @param obj
         * @returns {boolean}
         */
        "hasProduct" : function (arr, obj) {
            for (i in arr) {
                if (this.isEqual(arr[i], obj)) {
                    return true;
                }
            }

            return false;
        },

        /**
         * @param object1
         * @param object2
         * @returns {boolean}
         */
        "isEqual" : function (object1, object2) {
            if (typeof object1 === 'object' && typeof object2 === 'object') {
                return JSON.stringify(object1) === JSON.stringify(object2);
            }

            return false;
        }
    };

    // Устанавливаем событие на загрузку страницы
    document.addEventListener('DOMContentLoaded', function () {
        bxEcommerce.parse();

        // Устанавливаем событие на изменение страницы
        document.body.addEventListener("DOMNodeInserted",function(e) {
            console.log('changes');
            clearTimeout(window.ecommerceTimer);
            var containrers = document.querySelectorAll('[data-etype]');

            if (containrers.length > 0) {
                window.ecommerceTimer = setTimeout(function () {
                    console.log('страница изменена');
                    bxEcommerce.parse(containrers);
                }, 1000);
            }
        }, false);
    });
</script>
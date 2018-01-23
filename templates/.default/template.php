<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<script type="text/javascript" id="google_ecommerce">
    var BxEcommerce = function () {
        /**
         * Парсинг размеченных для ecommerce контейнеров
         *
         * @param containers
         */
        this.parse = function (containers) {
            containers = containers || document.querySelectorAll('[data-etype]');

            if (typeof containers === 'object' && containers.length === 0) return;

            var data = { "ecommerce" : {} }, product = {}, obj = this;

            Array.prototype.forEach.call(containers, function (container) {
                var type = container.getAttribute('data-etype');

                switch (type) {
                    case 'detail':
                        var detailProducts = data.ecommerce.detail = data.ecommerce.detail || [];

                        product = obj.getItemProductData(container);

                        if (!hasProduct(detailProducts, product)) { detailProducts.push(product); }
                        break;

                    case 'impressions':
                        var impressionsProducts = data.ecommerce.impressions = data.ecommerce.impressions || [],
                            position = (impressionsProducts.length > 0) ? impressionsProducts[impressionsProducts.length - 1].position + 1 : 0;

                        product = obj.getItemProductData(container, position);

                        if (!hasProduct(impressionsProducts, product)) { impressionsProducts.push(product); }

                        break;
                    case 'checkout':
                        data.event = 'checkout';
                        data.ecommerce.checkout = data.ecommerce.checkout || { "actionField" : { "step" : 1 } };

                        var checkoutProducts = data.ecommerce.checkout.products = data.ecommerce.checkout.products || [],
                            position = (checkoutProducts.length > 0) ? checkoutProducts[checkoutProducts.length - 1].position + 1 : 0;

                        product = obj.getCheckoutProductData(container, position);

                        if (!hasProduct(checkoutProducts, product)) { checkoutProducts.push(product); }

                        break;

                    case 'checkoutOption':

                        break;

                    case 'transaction':

                        break;
                }
            });

            sendData(dataLayer, data);
        };

        /**
         * Выборка данных из контейнера товара
         *
         * @param container
         * @param position
         * @returns {{position: *|number}}
         */
        this.getItemProductData = function (container, position) {
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
        };

        /**
         * Выборка данных из контейнера корзины
         *
         * @param container
         * @param position
         * @returns {{position: *|number}}
         */
        this.getCheckoutProductData = function (container, position) {
            position = position || 0;

            var product = { "position" : position },
                productTpl = {
                    "id" : "",
                    "name" : "",
                    "price" : "",
                    "brand" : "",
                    "category" : "",
                    "variant": "",
                    "dimension1": "",
                    "quantity": 0
                };

            for (var label in productTpl) {
                if (label.length > 0) {
                    product[label] = container.querySelector('[data-eproduct=' + label + ']').value;
                }
            }

            return product;
        };

        /**
         * @param arr
         * @param obj
         * @returns {boolean}
         */
        function hasProduct(arr, obj) {
            for (i in arr) {
                var obj1 = Object.assign({}, arr[i]),
                    obj2 = Object.assign({}, obj);
                obj1.position = obj2.position = 0;

                if (isEqual(obj1, obj2)) {
                    return true;
                }
            }

            return false;
        }

        /**
         * @param object1
         * @param object2
         * @returns {boolean}
         */
        function isEqual(object1, object2) {
            if (typeof object1 === 'object' && typeof object2 === 'object') {
                return JSON.stringify(object1) === JSON.stringify(object2);
            }

            return false;
        }

        /**
         * @param arr
         * @param data
         */
        function sendData(arr, data) {
            for (i in arr) {
                if (arr[i].ecommerce !== undefined) {
                    arr.splice(i, 1);
                }
            }

            arr.push(data);
        }
    };
</script>
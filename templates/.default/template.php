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

                        product = obj.getProductData(container);

                        if (!hasProduct(detailProducts, product)) {
                            obj.attachEvents(container, product);
                            detailProducts.push(product);
                        }
                        break;

                    case 'impressions':
                        var impressionsProducts = data.ecommerce.impressions = data.ecommerce.impressions || [];

                        product = obj.getProductData(container, getPosition(impressionsProducts));

                        if (!hasProduct(impressionsProducts, product)) {
                            obj.attachEvents(container);
                            impressionsProducts.push(product);
                        }
                        break;

                    case 'checkout':
                        data.event = 'checkout';
                        data.ecommerce.checkout = data.ecommerce.checkout || { "actionField" : { "step" : 1 } };
                        var checkoutProducts = data.ecommerce.checkout.products = data.ecommerce.checkout.products || [];

                        product = obj.getProductDataFull(container, getPosition(checkoutProducts));

                        if (!hasProduct(checkoutProducts, product)) {
                            obj.attachEvents(container);
                            checkoutProducts.push(product);
                        }
                        break;

                    case 'checkoutOption':
                        data.event = 'checkoutOption';
                        data.ecommerce.checkout_option = data.ecommerce.checkout_option || { "actionField" : { "step" : 2 } };

                        var options = obj.getCheckoutOptionData(container);

                        for (label in options) { data.ecommerce.checkout_option.actionField[label] = options[label]; }
                        break;

                    case 'transactionOrder':
                        data.event = 'transaction';
                        data.ecommerce.purchase = data.ecommerce.purchase || { "actionField" : { }, "products" : [] };

                        data.ecommerce.purchase.actionField = obj.getTransactionOrderData(container);

                        break;

                    case 'transactionProduct':
                        data.event = 'transaction';
                        data.ecommerce.purchase = data.ecommerce.purchase || { "actionField" : { }, "products" : [] };
                        var transactionProducts = data.ecommerce.purchase.products;

                        product = obj.getTransactionProductData(container, getPosition(impressionsProducts));

                        if (!hasProduct(transactionProducts, product)) {
                            obj.attachEvents(container);
                            transactionProducts.push(product);
                        }
                        break;
                }
            });

            sendData(data);
        };

        this.tools = {
            "addEvent" : function (evt, container, selector, func) {
                Array.prototype.forEach.call(container.querySelectorAll(selector), function (item) {
                    item[evt + selector] = item[evt + selector] || func;
                    item.removeEventListener('click', item[evt + selector]);
                    item.addEventListener('click', item[evt + selector], false);
                });
            }
        };

        this.events = {
            "productClick" : function (product) {console.log('productClick');
                product = product || {};
                var data = {
                    "event" : "productClick",
                    "ecommerce" : {
                        "click" : {
                            "actionField" : {
                                "list" : "homepahe"
                            },
                            "products" : [product]
                        }
                    }
                };

                sendData(data);
            },
            "addToCart" : function (product) {console.log('addToCart');
                product = product || {};
                var data = {
                    "event" : "addToCart",
                    "ecommerce" : {
                        "currencyCode" : "RUB", // @todo cделать выбор валюты
                        "add" : {
                            "products" : [product]
                        }
                    }
                };

                sendData(data);
            },
            "removeFromCart" : function (product) {console.log('removeFromCart');
                product = product || {};
                var data = {
                    "event" : "removeFromCart",
                    "ecommerce" : {
                        "currencyCode" : "RUB",
                        "remove" : {
                            "products" : [product]
                        }
                    }
                };

                sendData(data);
            }
        };

        /**
         * Выборка данных из контейнера товара
         *
         * @param container
         * @param position
         * @returns {{position: *|number}}
         */
        this.getProductData = function (container, position) {
            position = position || 0;

            var product = { "position" : position },
                productTpl = {
                    "id" : "",
                    "name" : "",
                    "price" : "",
                    "brand" : "",
                    "category" : ""
                };

            product = Object.assign(product, fillObjectByTemplate(productTpl, container));

            return product;
        };

        /**
         * Выборка данных из контейнера корзины
         *
         * @param container
         * @param position
         * @returns {{position: *|number}}
         */
        this.getProductDataFull = function (container, position) {
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

            product = Object.assign(product, fillObjectByTemplate(productTpl, container));

            return product;
        };

        /**
         * Выборка данных из контейнера доставки и оплаты
         *
         * @param container
         * @returns {{position: *|number}}
         */
        this.getCheckoutOptionData = function (container) {
            var optionsTpl = {
                    "option" : "",
                    "option2" : ""
                };

            return fillObjectByTemplate(optionsTpl, container);
        };

        /**
         * Выборка данных о заказе из контейнера информации о заказе
         *
         * @param container
         * @returns {{position: *|number}}
         */
        this.getTransactionOrderData = function (container) {
            var orderTpl = {
                    "id" : "",
                    "affiliation" : "",
                    "revenue" : "",
                    "tax" : "",
                    "shipping" : "",
                    "coupon" : ""
                };

            return fillObjectByTemplate(orderTpl, container);
        };

        /**
         * Выборка данных о товаре из контейнера информации о заказе
         *
         * @param container
         * @param position
         * @returns {{position: *|number}}
         */
        this.getTransactionProductData = function (container, position) {
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

            product = Object.assign(product, fillObjectByTemplate(productTpl, container));

            return product;
        };

        /**
         * Добавляет события к элементам
         *
         * @param container
         */
        this.attachEvents = function(container) {
            var parent = this,
                selectors = {
                    "detail" : {"event" : "click", "selector" : "[data-eproduct-event=detail]"},
                    "buy" : {"event" : "click", "selector" : "[data-eproduct-event=buy]"},
                    "removeFromCart" : {"event" : "click", "selector" : "[data-eproduct-event=remove_cart]"}
                };

            for (s in selectors) {
                switch (s) {
                    case 'detail':
                        this.tools.addEvent(selectors[s].event, container, selectors[s].selector, function (e) {
                            parent.events.productClick(parent.getProductData(container));
                        });
                        break;

                    case 'buy':
                        this.tools.addEvent(selectors[s].event, container, selectors[s].selector, function (e) {
                            parent.events.addToCart(parent.getProductDataFull(container));
                        });
                        break;

                    case 'removeFromCart':
                        this.tools.addEvent(selectors[s].event, container, selectors[s].selector, function (e) {
                            parent.events.removeFromCart(parent.getProductDataFull(container));
                        });
                        break;
                }
            }
        };

        /**
         * Заполняет объект, делая выборку по объекту-шаблону
         *
         * @param template
         * @param container
         * @return Object
         */
        function fillObjectByTemplate(template, container) {
            template = template || {};

            var resultObject = {};

            if (template.length === 0) return resultObject;

            for (var label in template) {
                if (label.length > 0) {
                    var item = container.querySelector('[data-eproduct=' + label + ']');

                    if (item !== null) {
                        resultObject[label] = item.value;
                    }
                }
            }

            return resultObject;
        }

        /**
         * Наличие объекта в массиве
         *
         * @param arr
         * @param obj
         * @returns {boolean}
         */
        function hasObject(arr, obj) {
            for (i in arr) {
                var obj1 = Object.assign({}, arr[i]),
                    obj2 = Object.assign({}, obj);
                delete obj1['gtm.uniqueEventId'];

                if (isEqual(obj1, obj2)) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Наличие товара в массиве
         *
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
         * Сравнение 2-х объектов
         *
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
         * Возвращает позицию следующего товара
         *
         * @param products
         * @returns {number}
         */
        function getPosition(products) {
            products = products || [];
            var position = 0;

            if (typeof products === 'object' && products.length === 0) {
                return position;
            }

            return (products.length > 0) ? products[products.length - 1].position + 1 : 0;
        }

        /**
         * Отправка данных в массив dataLayer
         *
         * @param data
         * @param clearOld
         */
        function sendData(data, clearOld) {
            clearOld = clearOld || false;
            var arr = dataLayer;

            if (clearOld === true) {
                for (i in arr) {
                    if (arr[i].ecommerce !== undefined) {
                        arr.splice(i, 1);
                    }
                }
            }

            // Если отакого объекта нет в массиве
            if (!hasObject(arr, data)) { arr.push(data); }
        }
    };
</script>
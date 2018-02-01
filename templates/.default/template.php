<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<? if ($arParams['mode'] !== 'ajax') $this->SetViewTarget('bitrix_ecommerce'); ?>
    <!--  START Code for component bitrix.ecommerce  -->
    <script type="text/javascript" id="bx_ecommerce">
        <? if (!empty($arResult['ecommerce'])): ?>
        window.dataLayer = (window.dataLayer || []);
        window.dataLayer.push({
            <? if (!empty($arResult['ecommerce']['event'])): ?>
            "event" : "<?= $arResult['ecommerce']['event'] ?>",
            <? endif; ?>
            "ecommerce" : {
                <? // impressions ?>
                <? if (!empty($arResult['ecommerce']['impressions']['products'])): ?>
                    "currencyCode" : "<?= $arResult['common']['currency'] ?>",
                    "impressions" : [
                        <? foreach ($arResult['ecommerce']['impressions']['products'] as $pos => $product) {
                            /* @var BxEcommerce\Product $product */
                            echo json_encode($product->getProduct()) . ',';
                        } ?>
                    ],
                <? endif; ?>
                <? // detail ?>
                <? if (!empty($arResult['ecommerce']['detail']['products'])):
                    $product = reset($arResult['ecommerce']['detail']['products']);
                ?>
                    "detail" : {
                        "products" :
                        <?
                        /* @var BxEcommerce\Product $product */
                        echo json_encode($product->getProduct()) . ',';
                        ?>
                    },
                <? endif; ?>
                <? // checkout ?>
                <? if (!empty($arResult['ecommerce']['checkout']['products'])): ?>
                    "checkout" : {
                        <? if (!empty($arResult['ecommerce']['checkout']['options'])): ?>
                            "actionField": {
                                <? foreach ($arResult['ecommerce']['checkout']['options'] as $name => $option): ?>
                                    "<?= $name ?>": "<?= $option ?>",
                                <? endforeach; ?>
                            },
                        <? endif; ?>
                        "products": [
                            <? foreach ($arResult['ecommerce']['checkout']['products'] as $pos => $product) {
                                /* @var BxEcommerce\Product $product */
                                echo json_encode($product->getFullProduct()) . ',';
                            } ?>
                        ]
                    },
                <? endif; ?>
                <? // checkoutOption ?>
                <? if (!empty($arResult['ecommerce']['checkoutOption'])): ?>
                    "checkout_option": {
                        <? if (!empty($arResult['ecommerce']['checkoutOption']['options'])): ?>
                            "actionField": {
                                <? foreach ($arResult['ecommerce']['checkoutOption']['options'] as $name => $option): ?>
                                "<?= $name ?>": "<?= $option ?>",
                                <? endforeach; ?>
                            },
                        <? endif; ?>
                    },
                <? endif; ?>
                <? // transaction ?>
                <? if (!empty($arResult['ecommerce']['transaction']['products'])): ?>
                    "purchase" : {
                        <? if (!empty($arResult['ecommerce']['transaction']['options'])): ?>
                            "actionField": {
                                "affiliation" : "<?= $arResult['common']['affiliation'] ?>",
                                <? foreach ($arResult['ecommerce']['transaction']['options'] as $name => $option): ?>
                                "<?= $name ?>": "<?= $option ?>",
                                <? endforeach; ?>
                            },
                        <? endif; ?>
                        "products": [
                            <? foreach ($arResult['ecommerce']['transaction']['products'] as $pos => $product)
                            {
                                /* @var BxEcommerce\Product $product */
                                echo json_encode($product->getFullProduct()) . ',';
                            } ?>
                        ]
                    },
                <? endif; ?>
            }
        });
        <? endif; ?>
        <? if ($arParams['mode'] !== 'top' && $arParams['mode'] !== 'ajax'): ?>
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
                    var product = getProduct(this),
                        quantity = undefined,
                        i = 0;

                    // ищем поле со значением кол-ва добавляемого товара
                    while (i < 10 && quantity === undefined) {
                        quantity = $(this).parent().find('[data-eproduct=quantity]').val();
                        elem = $(this).parent();
                        i++;
                    }

                    if (product !== false) {
                        if (quantity !== undefined) {
                            product['quantity'] = quantity;
                        }

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
        <? endif; ?>
    </script>
    <!--  END Code for component bitrix.ecommerce  -->
<? if ($arParams['mode'] !== 'ajax') $this->EndViewTarget(); ?>
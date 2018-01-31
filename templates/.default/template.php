<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<? if (!empty($arResult['ecommerce'])): ?>
    <? if ($arParams['mode'] !== 'ajax') $this->SetViewTarget('bitrix_ecommerce'); ?>
    <!--  START Code for component bitrix.ecommerce  -->
    <script type="text/javascript" id="bx_ecommerce">
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
        <? require_once 'script.php'; ?>
    </script>
    <!--  END Code for component bitrix.ecommerce  -->
    <? if ($arParams['mode'] !== 'ajax') $this->EndViewTarget(); ?>
<? endif; ?>
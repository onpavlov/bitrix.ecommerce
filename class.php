<?php

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Loader;
use Bitrix\Main\SiteTable;
use Bitrix\Sale\Internals;

class BitrixEcommerce extends CBitrixComponent
{
    const DEFAULT_CURRENCY = 'RUB';
    const DEFAULT_SITE_ID = 's1';

    const MODE_TOP = 'top';
    const MODE_FOOTER = 'footer';
    const MODE_AJAX = 'ajax';
    const MODE_INIT = 'init';

    private $modules = ['currency'];

    /**
     * @param $arParams
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function onPrepareComponentParams($arParams)
    {
        require_once __DIR__ . '/classes/Product.php';

        foreach ($this->modules as $module) {
            if (!Loader::includeModule($module)) {
                die('Cannot include module ' . $module);
            }
        }

        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->arParams['mode'] === self::MODE_INIT) { return; }

        $this->arResult['common'] = [
            'currency' => $this->getCurrency(),
            'affiliation' => $this->getAffiliation()
        ];
        $this->arResult['ecommerce'] = $this->getData();

        if ($this->arParams['mode'] === self::MODE_TOP) {
            $this->includeComponentTemplate('_empty');
        } else {
            $this->includeComponentTemplate();
        }
    }

    /**
     * @return string
     */
    private function getCurrency()
    {
        return CurrencyManager::getBaseCurrency()
            ? CurrencyManager::getBaseCurrency()
            : self::DEFAULT_CURRENCY;
    }

    /**
     * @return string
     */
    private function getAffiliation()
    {
        $siteId = empty($this->arParams['SITE_ID']) ? self::DEFAULT_SITE_ID : $this->arParams['SITE_ID'];
        $site = SiteTable::getById($siteId)->fetch();

        return $site['NAME'];
    }

    /**
     * @return mixed
     */
    private function getData()
    {
        global $bxEcommerce;
        return $bxEcommerce;
    }

    /**
     * @param $type
     * @param \BxEcommerce\Product $product
     * @return string
     */
    public static function addProduct($type, \BxEcommerce\Product $product)
    {
        global $bxEcommerce;
        $lastProduct = end($bxEcommerce[$type]['products']);

        if (!empty($lastProduct)) {
            $product->position = $lastProduct->position + 1;
        } else {
            $product->position = 0;
        }

        $bxEcommerce[$type]['products'][] = $product;
        return 'addProductItem(' . \Bitrix\Main\Web\Json::encode($product->getFullProduct()) . ');';
    }

    /**
     * @param $type
     * @param array $options
     */
    public static function addOptions($type, $options = [])
    {
        if (!empty($options)) {
            global $bxEcommerce;

            foreach ($options as $name => $option) {
                $bxEcommerce[$type]['options'][$name] = $option;
            }
        }
    }

    /**
     * @param string $event
     */
    public static function setEvent($event = '')
    {
        if (!empty($event)) {
            global $bxEcommerce;

            $bxEcommerce['event'] = $event;
        }
    }

    /**
     * @param int $orderId
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getTransactionOneClickCode($orderId = 0)
    {
        if (!$orderId) return '';

        $modules = ['sale', 'iblock'];

        foreach ($modules as $module) {
            if (!Loader::includeModule($module)) {
                die('Cannot include module ' . $module);
            }
        }

        $object = new self();
        $result = [];
        $order = Internals\OrderTable::getById($orderId)->fetch();
        $basketItems = Internals\BasketTable::getList(['filter' => ['ORDER_ID' => $orderId]]);

        if (!empty($order)) {
            $result['event'] = 'transactionOneClick';
            $result['ecommerce']['purchase']['actionField'] = [
                'id' => $order['ID'],
                'affiliation' => $object->getAffiliation(),
                'revenue' => $order['PRICE'],
                'tax' => '0.00',
                'shipping' => (float) $order['DELIVERY_PRICE']
            ];
        }

        $pos = $i =  0; $productsIds = [];

        while ($basketItem = $basketItems->fetch()) {
            $result['ecommerce']['purchase']['products'][$pos] = [
                'id' => $basketItem['PRODUCT_ID'],
                'name' => $basketItem['NAME'],
                'price' => $basketItem['PRICE'],
                'variant' => $basketItem['PRODUCT_ID'],
                'quantity' => $basketItem['QUANTITY'],
                'position' => $pos,
                'brand' => '""'
            ];
            $pos++;
            $productsIds[] = $basketItem['PRODUCT_ID'];
        }

        $products = \Bitrix\Iblock\ElementTable::getList([
            'filter' => ['ID' => $productsIds],
            'select' => ['ID', 'NAME', 'IBLOCK_SECTION.NAME']
        ]);

        while ($product = $products->fetch()) {
            $result['ecommerce']['purchase']['products'][$i]['category'] = $product['IBLOCK_ELEMENT_IBLOCK_SECTION_NAME'];
            $i++;
        }

        return 'dataLayer.push(' . json_encode($result) . ');';
    }
}
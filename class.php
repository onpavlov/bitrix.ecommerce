<?php

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Loader;
use Bitrix\Main\SiteTable;

class BitrixEcommerce extends CBitrixComponent
{
    const DEFAULT_CURRENCY = 'RUB';
    const DEFAULT_SITE_ID = 's1';

    private $modules = ['currency'];

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
        if ($this->arParams['mode'] === 'init') { return; }

        $this->arResult['common'] = [
            'currency' => $this->getCurrency(),
            'affiliation' => $this->getAffiliation()
        ];
        $this->arResult['ecommerce'] = $this->getData();

        if (!empty($this->arResult['ecommerce'])) {
            $this->includeComponentTemplate();
        } else {
            $this->includeComponentTemplate('_empty');
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
        $lastProduct = end($bxEcommerce[$type]);

        if (!empty($lastProduct)) {
            $product->position = $lastProduct->position + 1;
        } else {
            $product->position = 0;
        }

        $bxEcommerce[$type]['products'][] = $product;
        return 'addProductItem(' . json_encode($product->getFullProduct()) . ');';
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
}
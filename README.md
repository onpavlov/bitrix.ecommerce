# bitrix.ecommerce
Bitrix компонент для внедрения кодов электронной коммерции (google ecommerce)

### Настройка работы компонента

Для корректной работы компонента необходимо:

* В файле шаблона header.php разместить код подключения компонента перед подключением скрипта 
Google Tag Manager
```PHP
<? $APPLICATION->IncludeComponent('custom:bitrix.ecommerce', '', ['mode' => 'top']); ?>
```
* В файле шаблона footer.php разместить код подключения компонента
```PHP
<? $APPLICATION->IncludeComponent('custom:bitrix.ecommerce', '', []); ?>
```
* Далее разместить коды для передачи данных в компонент в зависимости от типа страницы, следуя 
инструкцям ниже

#### Важно:
Если данные загружаются через AJAX, необходимо
* в начале шаблона инициализировать компонент
```PHP
<? $APPLICATION->IncludeComponent('custom:bitrix.ecommerce', '', ['mode' => 'init']); ?>
```
* в конце шаблона подключить компонент для генерации кода
```PHP
<? $APPLICATION->IncludeComponent('custom:bitrix.ecommerce', '', ['mode' => 'ajax']); ?>
```

#### 1. Карточка товара (detail)

* На странице карточки товара разместить код и передать в него параметры
```PHP
    <script type="text/javascript">
    <? // START Code for component bitrix.ecommerce
    echo BitrixEcommerce::addProduct('detail', new BxEcommerce\Product([
        'id' => 123,
        'name' => 'test',
        'price' => 100.00,
        'brand' => 1,
        'category' => 2,
        'variant' => 123,
        'dimension1' => '',
        'quantity' => 1
    ])); ?>
    </script>
    <input type="hidden" name="eproduct_id" value="<?= $arResult['ID'] ?>">
    <? // END Code for component bitrix.ecommerce ?>
```

#### 2. Сопутствующие товары (impressions)

Если на странице отображается список товаров (каталог товаров, просмотренные товары, рекомендуемые товары) - они 
размечаются как сопутствующие товары (impressions).

* На странице со списком товаров для **каждого** товара разместить код и передать в него параметры
```PHP
    <script type="text/javascript">
    <? // START Code for component bitrix.ecommerce
    echo BitrixEcommerce::addProduct('impressions', new BxEcommerce\Product([
        'id' => 123,
        'name' => 'test',
        'price' => 100.00,
        'brand' => 1,
        'category' => 2,
        'variant' => 123,
        'dimension1' => '',
        'quantity' => 1
    ])); ?>
    </script>
    <input type="hidden" name="eproduct_id" value="<?= $arResult['ID'] ?>">
    <? // END Code for component bitrix.ecommerce ?>
```

#### 3. Корзина товаров (checkout)

* На странице корзины товаров в шаблон добавить код 
```PHP
<? BitrixEcommerce::addOptions('checkout', ['step' => 1]);
BitrixEcommerce::setEvent('checkout'); ?>
```
* В списке товаров для **каждого** товара разместить код и передать в него параметры
```PHP
    <script type="text/javascript">
    <? // START Code for component bitrix.ecommerce
    echo BitrixEcommerce::addProduct('impressions', new BxEcommerce\Product([
        'id' => 123,
        'name' => 'test',
        'price' => 100.00,
        'brand' => 1,
        'category' => 2,
        'variant' => 123,
        'dimension1' => '',
        'quantity' => 1
    ])); ?>
    </script>
    <input type="hidden" name="eproduct_id" value="<?= $arResult['ID'] ?>">
    <? // END Code for component bitrix.ecommerce ?>
```

#### 4. Оформление заказа (checkoutOption)

* На странице оформления заказа / оплаты заказа добавить код
```PHP
<? // START Code for component bitrix.ecommerce
    BitrixEcommerce::setEvent('checkoutOption');
    BitrixEcommerce::addOptions('checkoutOption', [
        'step' => 2,
        'option' => '',
        'option2' => '',
    ]);
// END Code for component bitrix.ecommerce ?>
```

#### 5. Страница "Спасибо за покупку" (transaction)

* На финальной странице оформления заказа добавить код
```PHP
<? // START Code for component bitrix.ecommerce
    BitrixEcommerce::setEvent('transaction');
    BitrixEcommerce::addOptions('transaction', [
        'id' => $arResult['ORDER']['ID'],
        'revenue' => $arResult['ORDER']['PRICE'],
        'tax' => '0.00',
        'shipping' => $arResult['ORDER']['PRICE_DELIVERY']
    ]);
// END Code for component bitrix.ecommerce ?>
```
* В списке товаров для **каждого** товара разместить код и передать в него параметры
```PHP
    <script type="text/javascript">
    <? // START Code for component bitrix.ecommerce
    echo BitrixEcommerce::addProduct('impressions', new BxEcommerce\Product([
        'id' => 123,
        'name' => 'test',
        'price' => 100.00,
        'brand' => 1,
        'category' => 2,
        'variant' => 123,
        'dimension1' => '',
        'quantity' => 1
    ])); ?>
    </script>
    <input type="hidden" name="eproduct_id" value="<?= $arResult['ID'] ?>">
    <? // END Code for component bitrix.ecommerce ?>
```

#### 6. Событие клика по товару (productClick)

Для всех ссылок перехода к детальной странице 
товара добавить атрибут **data-eproduct-event="detail"**

#### 7. Событие добавления товара в корзину (addToCart)

* Для всех ссылок перехода к детальной странице 
товара добавить атрибут **data-eproduct-event="buy"**
* Если на странице есть поле с количеством товара, добавить к нему атрибут **data-eproduct="quantity"**

#### 8. Событие удаления товара из корзины (removeFromCart)

Для всех ссылок удаления товара из корзины 
добавить атрибут **data-eproduct-event="remove_cart"**

#### 8. Событие оформления заказа в один клик (transactionOneClick)

Для всех ссылок покупки в один клик (если есть) 
добавить атрибут **data-eproduct-event="oneclick_buy"**
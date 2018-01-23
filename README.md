# bitrix.ecommerce
Bitrix компонент для внедрения кодов электронной коммерции (google ecommerce)

### Настройка работы компонента

Для корректной работы компонента необходимо разметить код на сайте 
и передать туда все необходимые параметры.

##### 1. Карточка товара (detail)

* На странице карточки товара для контейнера, содержащего информацию 
о товаре добавить data-атрибут **data-etype="detail"**
* Внутрь контейнера поместить HTML-код и передать значения в аттрибут value
```HTML
    <!--  START Code for component bitrix.ecommerce  -->
    <input type="hidden" data-eproduct="id" value="">
    <input type="hidden" data-eproduct="name" value="">
    <input type="hidden" data-eproduct="price" value="">
    <input type="hidden" data-eproduct="category" value="">
    <input type="hidden" data-eproduct="brand" value="">
    <!--  END Code for component bitrix.ecommerce  -->
```

##### 2. Сопутствующие товары (impressions)

Если на странице отображается список товаров (каталог товаров, просмотренные товары, рекомендуемые товары) - они 
размечаются как сопутствующие товары (impressions).

* На странице со списком товаров для **каждого** контейнера, содержащего информацию 
о товаре добавить data-атрибут **data-etype="impressions"**
* Внутрь контейнера поместить HTML-код и передать значения в аттрибут value
```HTML
    <!--  START Code for component bitrix.ecommerce  -->
    <input type="hidden" data-eproduct="id" value="">
    <input type="hidden" data-eproduct="name" value="">
    <input type="hidden" data-eproduct="price" value="">
    <input type="hidden" data-eproduct="category" value="">
    <input type="hidden" data-eproduct="brand" value="">
    <!--  END Code for component bitrix.ecommerce  -->
```

##### 3. Корзина товаров (checkout)

* На странице корзины товаров для **каждого** контейнера, содержащего информацию 
о товаре добавить data-атрибут **data-etype="checkout"**
* Внутрь контейнера поместить HTML-код и передать значения в аттрибут value
```HTML
    <!--  START Code for component bitrix.ecommerce  -->
    <input type="hidden" data-eproduct="id" value="">
    <input type="hidden" data-eproduct="name" value="">
    <input type="hidden" data-eproduct="price" value="">
    <input type="hidden" data-eproduct="category" value="">
    <input type="hidden" data-eproduct="brand" value="">
    <input type="hidden" data-eproduct="variant" value="">
    <input type="hidden" data-eproduct="dimension1" value="">
    <input type="hidden" data-eproduct="quantity" value=""> // может отсутствовать читай след. пункт
    <!--  END Code for component bitrix.ecommerce  -->
```
* Если у товара уже есть input поле со значением количества товара в корзине, добавить ему 
атрибут **data-eproduct="quantity"** иначе 
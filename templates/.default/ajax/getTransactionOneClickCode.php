<?php include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent('custom:bitrix.ecommerce', '', ['mode' => 'init']);

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$code = BitrixEcommerce::getTransactionOneClickCode($request->get('orderId'));

if (!empty($code)) {
    die(json_encode(['success' => true, 'code' => $code]));
}

die(json_encode(['success' => false]));
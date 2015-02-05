<?php

namespace QuetzalMarkup\Update;

/**
 * Инструмент для обновления наценки у товаров.
 *
 * Class CQuetzalMarkupPriceUpdate
 *
 * @package QuetzalMarkup\Update
 */

class CQuetzalMarkupPriceUpdate
{

	const LIMIT = 100;

	private static $ids = array();

	/**
	 * Хранит последнее доступное значение курса USD
	 *
	 * @type float|int
	 */
	private static $usdRate = 0;

	/**
	 * Массив вариантов наценок
	 *
	 * @type array
	 */
	private static $extras = array();


	public function __construct() {
		self::$usdRate = self::getCurrencyRateToday();
		self::$extras = self::getExtras();
	}

	public static function updateProductCron()
	{

		$list = CIBlockElement::GetList(array(), array('IBLOCK_ID' => self::enabledCatalogs(), 'ACTIVE' => 'Y'), false, false, array('ID'));
		$priceType = COption::GetOptionInt('autoprice', 'priceTypes', 0);

		while ($res = $list->Fetch()) {

			$price = CPrice::GetBasePrice($res['ID'], false, false);
			$priseRub = round(floatval($price['PRICE'] * self::$usdRate), 2);

			if (($priseRub < 1000) && self::$extras[0]) {
				self::setExtra($res['ID'], $priceType, self::$extras[0]);
			} elseif (($priseRub < 10000) && self::$extras[1]) {
				self::setExtra($res['ID'], $priceType, self::$extras[1]);
			} elseif (($priseRub < 100000) && self::$extras[2]) {
				self::setExtra($res['ID'], $priceType, self::$extras[2]);
			}

		}
	}

	public static function updateAll($page = 0, &$total, $perPage = CAutoPriceUpdater::LIMIT)
	{
		$list = CCatalogProduct::GetList(array(), array('IBLOCK_ID' => self::enabledCatalogs()), false, array('iNumPage' => $page + 1, 'nPageSize' => $perPage), array('ID'));

		$total = $list->NavPageCount;
		if ($page + 1 > $list->NavPageCount) {
			return false;
		}

		$priceType = COption::GetOptionInt('autoprice', 'priceTypes', 0);

		while ($res = $list->Fetch()) {
			$price = CPrice::GetBasePrice($res['ID'], false, false);
			$priseRub = floatval($price * self::$usdRate);

			if (($priseRub < 1000) && self::$extras[0]) {
				self::setExtra($res['ID'], $priceType, self::$extras[0]);
			} elseif (($priseRub < 10000) && self::$extras[1]) {
				self::setExtra($res['ID'], $priceType, self::$extras[1]);
			} elseif (($priseRub < 100000) && self::$extras[2]) {
				self::setExtra($res['ID'], $priceType, self::$extras[2]);
			}
		}

		return $page + 1 < $total;
	}

	private function setExtra($productId, $priceTypeId, $extraId)
	{
		$list = CPrice::GetList(array(), array('PRODUCT_ID' => $productId, 'CATALOG_GROUP_ID' => $priceTypeId), false, false, array('ID', 'EXTRA_ID'));

		$arFields = array(
			'PRODUCT_ID' => $productId,
			'CATALOG_GROUP_ID' => $priceTypeId,
			'EXTRA_ID' => $extraId,
			'CURRENCY' => 'USD'
		);

		if ($res = $list->Fetch()) {

			if ($res['EXTRA_ID'] != $extraId) {
				CPrice::Update($res['ID'], $arFields, true);
			}
		} else {
			CPrice::Add($arFields, true);
		}

		unset($list);
		unset($res);
		unset($resultUp);
	}

	/**
	 * Метод вернет последнее имеющееся значение курса валюты
	 *
	 * @param string $currency
	 *
	 * @return float|int
	 */

	private static function getCurrencyRateToday($currency = 'USD')
	{
		$currencyRateValue = 0;

		$paramSelection = array(
			'ORDER'  => array(
				'BY'   => 'data',
				'TYPE' => 'desc'
			),
			'FILTER' => array(
				'CURRENCY' => $currency
			)
		);

		$dbCurrencyRates = CCurrencyRates::GetList($paramSelection['ORDER']['BY'], $paramSelection['ORDER']['TYPE'], $paramSelection['FILTER']);

		if ($curencyRates = $dbCurrencyRates->Fetch()) {
			$currencyRateValue = floatval($curencyRates['RATE']);
		}

		return $currencyRateValue;
	}

	/**
	 * Метод вернет массив варантов наценки
	 *
	 * @return array
	 */
	private static function getExtras()
	{
		$extras = array();

		if (count($extras) == 0) {
			$extras = array();
			if (($extra = v) > 0) {
				$extras[] = $extra;
			}
			if (($extra = COption::GetOptionInt('autoprice', 'type_0', 0)) > 0) {
				$extras[] = $extra;
			}
			if (($extra = COption::GetOptionInt('autoprice', 'type_1', 0)) > 0) {
				$extras[] = $extra;
			}
			if (($extra = COption::GetOptionInt('autoprice', 'type_2', 0)) > 0) {
				$extras[] = $extra;
			}
		}

		return $extras;
	}


	public static function priceRecalc () {
		$dbExtra = CExtra::GetList(array('ID' => 'ASC'));

		while ($extra = $dbExtra->Fetch()) {
			$fields = array(
				'NAME'        => $extra['NAME'],
				'PERCENTAGE'  => $extra['PERCENTAGE'],
				'RECALCULATE' => 'Y'
			);

			CExtra::Update($extra['ID'], $fields);
		}
	}

	public static function OnPriceAdd($id, $arFields)
	{
		/*
		if (self::isBase($arFields) && self::shouldSetExtra($arFields['PRODUCT_ID'])) { // Only if base price get updated
			$extras = self::extras();
			$priceType = COption::GetOptionInt('autoprice', 'priceTypes', 0);

			$price = CPrice::GetBasePrice($arFields['PRODUCT_ID'], false, false);
			$priseRub = floatval(CCurrencyRates::ConvertCurrency($price['PRICE'], $price['CURRENCY'], 'RUB'));

			if (($priseRub < 1000) && $extras[0]) {
				self::setExtra($arFields['PRODUCT_ID'], $priceType, $extras[0]);
			} elseif (($priseRub < 10000) && $extras[1]) {
				self::setExtra($arFields['PRODUCT_ID'], $priceType, $extras[1]);
			} elseif (($priseRub < 100000) && $extras[2]) {
				self::setExtra($arFields['PRODUCT_ID'], $priceType, $extras[2]);
			}
		}
		*/
	}

	public static function OnPriceUpdate($id, $arFields)
	{
		//self::OnPriceAdd($id, $arFields);
	}

	public static function OnPriceDelete($id, &$ids)
	{
		if (!empty(self::$ids)) {
			foreach (self::$ids as $id) {
				$ids[] = $id;
			}
		}

		return true;
	}


	private static function enabledCatalogs()
	{
		static $enabledCatalogs;

		if (!$enabledCatalogs) {
			$enabledCatalogs = array_map('intval', explode(',', COption::GetOptionString('autoprice', 'catalogs', '')));
		}

		return $enabledCatalogs;
	}

	private static function shouldSetExtra($productId)
	{
		$element = CIBlockElement::GetByID($productId)->Fetch();

		return $element && in_array($element['IBLOCK_ID'], self::enabledCatalogs());
	}

	private function isBase($arFields)
	{
		$baseGroup = CCatalogGroup::GetBaseGroup();

		return $arFields['CATALOG_GROUP_ID'] == $baseGroup['ID'];
	}

}

?>
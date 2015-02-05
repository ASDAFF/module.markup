<?php

namespace QuetzalMarkup\Options;

/**
 * Инструмент для работы с параметрами групп наценок.
 *
 * Class CQuetzalMarkupPriceOptionsEdit
 *
 * @package QuetzalMarkup\Options
 */

class CQuetzalMarkupPriceOptionsEdit
{
	/**
	 * Метод вернет ID типа цены, который сохранен в настройках
	 *
	 * @return array
	 */
	function getSelectedType()
	{
		return COption::GetOptionInt('autoprice', 'priceTypes', 0);
	}

	/**
	 * Метод вернет список типов цен
	 *
	 * @return array
	 */
	function getPriceTypeList()
	{
		$result = array();
		$selectedType = getSelectedType();

		$dbCatalogGroup = CCatalogGroup::GetList(array('SORT' => 'ASC'), array('BASE' => 'N'), false, false, array('ID', ' NAME', 'NAME_LANG'));

		while ($type = $dbCatalogGroup->GetNext()) {
			if ($type['ID'] == $selectedType) {
				$type['SELECTED'] = true;
			}
			$result[] = $type;
		}

		return $result;
	}

	/**
	 * Метод вернет массив ID каталогов, сохраненных в настройках
	 *
	 * @return array
	 */
	function getSelectedCatalog()
	{
		$result = array();

		$result = array_map('intval', explode(',', COption::GetOptionString('autoprice', 'catalogs', '')));

		return $result;
	}

	/**
	 * Метод вернет список инфоблоков которые являются торговыми каталогами
	 *
	 * @return array
	 */
	function getCatalogList()
	{
		$result = array();
		$selectedCatalog = getSelectedCatalog();

		$dbCatalog = CCatalog::GetList(array(), array(), false, false, array('ID', 'NAME'));

		while ($catalog = $dbCatalog->GetNext()) {
			if (in_array($catalog['ID'], $selectedCatalog)) {
				$catalog['SELECTED'] = true;
			}

			$result[] = $catalog;
		}

		return $result;
	}

	/**
	 * Метод вернет массив параметров из инфоблока "Параметры Автонаценки".
	 *
	 * @return array
	 */
	function getOptions () {

		$filterParams = array('ACTIVE' => 'Y', 'IBLOCK_CODE' => 'autoprice_options');
		$orderParams = array('SORT' => 'ASC');
		$selectParams = array('ID', 'IBLOCK_SECTION_ID', 'NAME', 'CODE', 'PROPERTY_PARAM', 'PROPERTY_MARKUP');

		$result = array();
		$iBlockElement = new CIBlockElement();

		$dbElements = $iBlockElement->GetList($orderParams, $filterParams, false, false, $selectParams);

		while ($element = $dbElements->GetNext()) {
			$result[] = $element;
		}

		return $result;
	}

	/**
	 * Метод getGroupsOfParameters вернет массив групп параметров из инфоблока "Параметры Автонаценки".
	 *
	 * @return array
	 */
	function getGroupsOfParameters () {

		$filterGroups = array('ACTIVE' => 'Y', 'IBLOCK_CODE' => 'autoprice_options');
		$orderGroups = array('SORT' => 'ASC');
		$selectGroups = array('ID', 'NAME', 'CODE');

		$result = array();
		$iBlockSection = new CIBlockSection();

		$dbSections = $iBlockSection->GetList($orderGroups, $filterGroups, false, $selectGroups, false);
		while ($section = $dbSections->GetNext()) {
			$result[$section['ID']] = $section;
		}

		return $result;
	}

	/**
	 * Метод вернет массив параметров, из инфоблока "Параметры Автонаценки", разбитый на группы.
	 *
	 * @return array
	 */
	function getOptionsList () {

		$options = getOptions();
		$groupsOfOptions = getGroupsOfParameters();

		$result = array();

		foreach ($groupsOfOptions as $group) {
			$result[$group['CODE']] = array();
		}

		foreach ($options as $option) {
			$codeGroupsOfOption = $groupsOfOptions[$option['IBLOCK_SECTION_ID']]['CODE'];
			$result[$codeGroupsOfOption][] = $option;
		}

		return $result;
	}
}
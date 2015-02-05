<?php
IncludeModuleLangFile(__FILE__);

class quetzal_markup extends CModule
{

	var $MODULE_ID = 'quetzal.markup';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function quetzal_markup()
	{

		$arModuleVersion = array();

		include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'version.php';

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		} else {
			$this->MODULE_VERSION = CURRENCY_VERSION;
			$this->MODULE_VERSION_DATE = CURRENCY_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage('QTZ_MARKUP_INSTALL_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('QTZ_MARKUP_INSTALL_DESCRIPTION');
	}

	function DoInstall()
	{
		global $APPLICATION;

		RegisterModule('quetzal.markup');

		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/quetzal.markup/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/quetzal.markup/install/js', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/', true, true);

		$APPLICATION->IncludeAdminFile(GetMessage('QTZ_MARKUP_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/quetzal.markup/install/step1.php');
	}

	function DoUninstall()
	{
		global $APPLICATION;

		UnRegisterModule('quetzal.markup');

		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/quetzal.markup/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/quetzal.markup/install/js', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js');

		$APPLICATION->IncludeAdminFile(GetMessage('QTZ_MARKUP_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/quetzal.markup/install/unstep1.php');
	}

}

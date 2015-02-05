<?php
IncludeModuleLangFile(__FILE__);

$aMenu = array(
	'parent_menu' => 'global_menu_services',
	'section'     => 'quetzal_markup',
	'sort'        => 600,
	'text'        => GetMessage('QTZ_MARKUP_SERVICE'),
	'title'       => GetMessage('QTZ_MARKUP_SERVICE'),
	'icon'        => 'sale_menu_icon_catalog',
	'page_icon'   => 'sale_menu_icon_catalog',
	'items_id'    => 'quetzal_markup_update',
	'items'       => array(
		array(
			'text'  => GetMessage('QTZ_MARKUP_MENU_UPDATE'),
			'url'   => '/bitrix/admin/markup_update.php?lang=' . LANG,
			'title' => GetMessage('QTZ_MARKUP_MENU_UPDATE')
		)
	)
);

return $aMenu;
<?php
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
CModule::IncludeModule('sale');
CModule::IncludeModule('currency');

CModule::AddAutoloadClasses('quetzal.markup',
	array(
		'CQuetzalMarkupPriceUpdate' => 'classes/general/CQuetzalMarkupPriceUpdate.php',
		'CQuetzalMarkupPriceOptionsEdit' => 'classes/general/CQuetzalMarkupPriceOptionsEdit.php',
	)
);
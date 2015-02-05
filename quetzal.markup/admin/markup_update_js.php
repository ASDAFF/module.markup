<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/quetzal.markup/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_js.php');

CModule::IncludeModule('quetzal.markup');

$page = (int)$_REQUEST['page'];
$total = 0;

if (QuetzalMarkup\Update\CQuetzalMarkupPriceUpdate::updateAll($page, $total)):?>
	markup_progress(<?= $page + 1 ?>, <?= $total ?>);
<? else: ?>
	markup_done();
<? endif;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin_js.php'); ?>
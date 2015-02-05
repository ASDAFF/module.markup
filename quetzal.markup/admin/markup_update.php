<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/quetzal.markup/prolog.php';

IncludeModuleLangFile(__FILE__);

$APPLICATION->SetTitle(GetMessage('QTZ_MARKUP_UPDATE_TITLE'));

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

CModule::IncludeModule('quetzal.markup');

$aTabs = array(
	array(
		'DIV'   => 'update',
		'TAB'   => GetMessage('QTZ_MARKUP_UPDATE_TAB'),
		'TITLE' => GetMessage('QTZ_MARKUP_UPDATE_TAB_TITLE'),
		'ICON'  => 'main_user_edit'
	)
);

$tabControl = new CAdminTabControl('quetzal.markup', $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>

<p><?= GetMessage('QTZ_MARKUP_UPDATE_PROGRESS') ?> <span id="markup-progress">0%</span></p>

<script type="text/javascript">
	var markup_page = 0;
	var markup_running = false;

	function markup_update_progress(page, total) {
		BX('markup-progress').innerHTML = Math.round(page * 100 / total) + '%';
	}

	function markup_progress(page, total) {

		if (markup_running) {
			BX.ajax({url: 'markup_update_js.php' + '?page=' + page, dataType: 'script'});
		}

		markup_page = page;
		markup_update_progress(page, total);
	}

	function markup_done() {
		BX('markup-run').disabled = false;
		BX('markup-suspend').disabled = true;

		markup_update_progress(1, 1);
		markup_page = 0;
	}

	function markup_run() {
		BX('markup-run').disabled = true;
		BX('markup-suspend').disabled = false;

		markup_running = true;
		BX.ajax({url: 'markup_update_js.php' + '?page=' + markup_page, dataType: 'script'});
	}

	function markup_suspend() {
		BX('markup-run').disabled = false;
		BX('markup-suspend').disabled = true;

		markup_running = false;
	}
</script>

<?php $tabControl->Buttons(); ?>
<input id="markup-run" type="button" value="<?= GetMessage('QTZ_MARKUP_IMPORT_RUN') ?>" title="" onClick="markup_run()" />
<input id="markup-suspend" type="button" value="<?= GetMessage('QTZ_MARKUP_IMPORT_SUSPEND') ?>" title="" onClick="markup_suspend()" disabled="disabled" />
<?php $tabControl->End(); ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php'; ?>

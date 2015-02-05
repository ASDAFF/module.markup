<?php

if (!check_bitrix_sessid()) {
	return;
}
global $errors;

echo CAdminMessage::ShowNote(GetMessage('MOD_INST_OK'));
?>

<form action="<?= $APPLICATION->GetCurPage() ?>">
	<p>
		<input type="hidden" name="lang" value="<?= LANG ?>" />
		<input type="submit" name="" value="<?= GetMessage('MOD_BACK') ?>" />
	</p>

<form>

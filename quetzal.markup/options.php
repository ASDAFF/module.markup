<?php
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/quetzal.markup/admin/menu.php');
IncludeModuleLangFile(__FILE__);

$APPLICATION->AddHeadScript('/bitrix/js/quetzal.markup/edit_params.js');

CModule::IncludeModule('iblock');
CModule::IncludeModule('quetzal.markup');


$markupOptionsList = QuetzalMarkup\Options\CQuetzalMarkupPriceOptionsEdit::getOptionsList ();

$aTabs = array(array('DIV' => 'config', 'TAB' => GetMessage('QTZ_MARKUP_CONFIG_TAB'), 'TITLE' => GetMessage('QTZ_MARKUP_CONFIG_TITLE'),));
$tabControl = new CAdminTabControl('markup_tab_controls', $aTabs);
$aMenu = array(array('TEXT' => GetMessage('QTZ_MARKUP_MENU_UPDATE'), 'LINK' => 'markup_update.php?lang=' . LANGUAGE_ID, 'TITLE' => GetMessage('QTZ_MARKUP_MENU_UPDATE'),));
$context = new CAdminContextMenu($aMenu);
$context->Show();

echo '<pre>';
print_r($_REQUEST);
echo '</pre>';

if ($REQUEST_METHOD == 'POST' && strlen($Update) > 0 && check_bitrix_sessid()) {

	// Обновление параметра ID целевого каталога
	COption::SetOptionString('markup', 'catalogs', implode(',', $_REQUEST['catalogs']));

	// Обновление параметра "Тип цены"
	COption::SetOptionInt('markup', 'priceTypes', $_REQUEST['priceTypes']);

	if (strlen($Update) > 0 && strlen($_REQUEST['back_url_settings']) > 0) {
		LocalRedirect($_REQUEST['back_url_settings']);
	} else {
		LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . urlencode($mid) . '&lang=' . urlencode(LANGUAGE_ID) . '&back_url_settings=' . urlencode($_REQUEST['back_url_settings']) . '&' . $tabControl->ActiveTabParam());
	}
}

$tabControl->Begin();
?>
<form method='post' action=''>
	<?php $tabControl->BeginNextTab(); ?>
	<tr>
		<td valign='middle' width='50%'>
			<label for='catalogs[]'><?= GetMessage('QTZ_MARKUP_OPTION_CATALOGS') ?></label>
		</td>
		<td valign='top' width='50%'>
			<select multiple='multiple' size='2' name='catalogs[]'>
				<?php foreach (getCatalogList() as $catalog) { ?>
					<option value='<?= $catalog['ID'] ?>' <?php if ($catalog['SELECTED']): ?>selected='selected'<? endif ?>><?= htmlspecialchars($catalog['NAME']) ?></option>
				<?php } ?>
			</select>
		</td>
	</tr>

	<tr>
		<td valign='middle' width='50%'>
			<label for='priceTypes'><?= GetMessage('QTZ_MARKUP_OPTION_PRICE_TYPE') ?></label>
		</td>
		<td valign='top' width='50%'>
			<select size='1' name='priceTypes'>
				<?php foreach (getPriceTypeList() as $priceTypes) { ?>
					<option value='<?= $priceTypes['ID'] ?>' <?php if ($priceTypes['SELECTED']): ?>selected='selected'<? endif ?>><?= htmlspecialchars($priceTypes['NAME_LANG']) ?></option>
				<?php } ?>
			</select>
		</td>
	</tr>

	<?foreach ($markupOptionsList as $group => $options) :?>
		<?switch ($group) :
			case 'prices': ?>
				<tr>
					<td valign='midle' align='center' width='50%' colspan='2'><b>Наценка для ценовых категорий товаров:</b></td>
				</tr>
				<?foreach ($options as $option) :?>
					<tr>
						<td valign='middle' width='50%'>
							<label for='type_0'><?=$option['PROPERTY_PARAM_VALUE']?></label>
						</td>
						<td valign='top' width='50%'>
							<?= CExtra::SelectBox($option['CODE'], $option['PROPERTY_MARKUP_VALUE'], GetMessage('QTZ_MARKUP_NONE')) ?>
						</td>
					</tr>
				<?endforeach?>
				<?break;?>
			<?case 'sections': ?>
				<tr>
					<td colspan="2" valign="middle" align="center" style="padding: 20px 0px 10px 0px"><b>Разделы:</b></td>
				</tr>
				<?if (count($options) > 0) :?>
					<?foreach ($options as $option) :?>
						<tr>
							<td colspan="2" align="center" style="padding: 10px 0px">
								<table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="70%" id="markup_parm_section">
									<tr>
										<td>
											<span>ID Раздела:
												<input class="js_input_text" name="section_id[]" value="<?=$option['PROPERTY_PARAM_VALUE']?>" size="10" type="text" />
											</span>
											<span>
												<?= CExtra::SelectBox('section_markup[]', $option['PROPERTY_MARKUP_VALUE'], GetMessage('QTZ_MARKUP_NONE')) ?>
											</span>
											<input type="button" value="Удалить параметр" onclick="deleteRow(this, 'markup_parm_section')" />
										</td>
									</tr>
									<tr>
										<td style="padding: 20px 10px 10px 0px; text-align: right; vertical-align: bottom">
											<input type="button" value="Добавить параметр" onclick="addNewRow('markup_parm_section')" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					<?endforeach?>
				<?else: ?>
					<tr>
						<td colspan="2" align="center" style="padding: 10px 0px">
							<table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="70%" id="markup_parm_section">
								<tr>
									<td>
										<span>ID Раздела:
											<input class="js_input_text" name="section_id[]" value="" size="10" type="text" />
										</span>
										<span>
											<?= CExtra::SelectBox('section_markup[]', COption::GetOptionInt('markup', 'type_3', 0), GetMessage('QTZ_MARKUP_NONE')) ?>
										</span>
										<input type="button" value="Удалить параметр" onclick="deleteRow(this, 'markup_parm_section')" />
									</td>
								</tr>
								<tr>
									<td style="padding: 20px 10px 10px 0px; text-align: right; vertical-align: bottom">
										<input type="button" value="Добавить параметр" onclick="addNewRow('markup_parm_section')" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				<?endif?>
				<?break;?>
			<?case 'brands': ?>
				<tr>
					<td colspan="2" valign="middle" align="center" style="padding: 20px 0px 10px 0px"><b>Бренды:</b></td>
				</tr>
				<?if (count($options) > 0) :?>
					<?foreach ($options as $option) :?>
						<td colspan="2" align="center" style="padding: 10px 0px">
							<table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="70%" id="markup_parm_section">
								<tr>
									<td>
										<span>Название "Бренда":
											<input class="js_input_text" name="brend_name[]" value="<?=$option['PROPERTY_PARAM_VALUE']?>" size="10" type="text" />
										</span>
										<span>
											<?= CExtra::SelectBox('section_markup[]', $option['PROPERTY_MARKUP_VALUE'], GetMessage('QTZ_MARKUP_NONE'))  ?>
										</span>
										<input type="button" value="Удалить параметр" onclick="deleteRow(this, 'markup_parm_section')" />
									</td>
								</tr>
								<tr>
									<td style="padding: 20px 10px 10px 0px; text-align: right; vertical-align: bottom">
										<input type="button" value="Добавить параметр" onclick="addNewRow('markup_parm_section')" />
									</td>
								</tr>
							</table>
						</td>
					<?endforeach?>
				<?else: ?>
					<tr>
						<td colspan="2" align="center" style="padding: 10px 0px">
							<table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="85%" id="markup_parm_brend">
								<tr>
									<td>
										<span>Название "Бренда":
											<input class="js_input_text" name="brend_name[]" value="" size="20" type="text" />
										</span>
										<span>
											<?= CExtra::SelectBox('brend_markup[]', COption::GetOptionInt('markup', 'type_3', 0), GetMessage('QTZ_MARKUP_NONE')) ?>
										</span>
										<input type="button" value="Удалить параметр" onclick="deleteRow(this, 'markup_parm_brend')" />
									</td>
								</tr>
								<tr>
									<td style="padding: 20px 10px 10px 0px; text-align: right; vertical-align: bottom">
										<input type="button" value="Добавить параметр" onclick="addNewRow('markup_parm_brend')" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				<?endif?>
				<?break;?>
		<?endswitch;?>
	<?endforeach;?>

	<?php // Tap for price types ?>
	<?php $tabControl->Buttons(); ?>
	<input type='submit' name='Update' value='<?= GetMessage('MAIN_SAVE') ?>' title='<?= GetMessage('MAIN_OPT_SAVE_TITLE') ?>'/>
	<?php if (strlen($_REQUEST['back_url_settings']) > 0): ?>
		<input type='button' name='Cancel' value='<?= GetMessage('MAIN_OPT_CANCEL') ?>' title='<?= GetMessage('MAIN_OPT_CANCEL_TITLE') ?>' onclick='window.location='<?= htmlspecialchars(CUtil::addslashes($_REQUEST['back_url_settings'])) ?>''/>
		<input type='hidden' name='back_url_settings' value='<?= htmlspecialchars($_REQUEST['back_url_settings']) ?>'/>
	<?php endif ?>
</form>

<?php $tabControl->End(); ?>

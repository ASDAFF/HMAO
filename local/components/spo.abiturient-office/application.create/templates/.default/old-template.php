<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php
use Spo\Site\Helpers\AbiturientOfficeUrlHelper;
use Spo\Site\Dictionaries\ApplicationFundingType;

$organizationData = $arResult['organizationWithAvailableSpecialties'];
?>

<div class="row">
    <div class="col-md-12">
    <?php if($arResult['success']):?>

        <p></p>
        <p class="text-success"><?= $arResult['success'] ?></p>
        <a class="btn btn-info" href="<?=AbiturientOfficeUrlHelper::getApplicationListUrl()?>">Список моих заявок</a>

    <?php else: ?>
    <form class="form-horizontal" method="post">
        <fieldset>

            <div class="form-group">
                <label class="col-md-4 control-label">Образовательная организация</label>
                <div class="col-md-8">
                    <p class="form-control-static"><?= $arResult['organizationWithAvailableSpecialties']['name'] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Дата подачи заявления</label>
                <div class="col-md-8">
                    <p class="form-control-static"><?= date('Y-m-d');?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Вид финансирования</label>
                <div class="col-md-8">
                    <?php foreach (ApplicationFundingType::getValuesArray() as $key => $value):?>
                        <label class="radio-inline" >
                            <input name="Application[applicationFundingType]" value="<?=$key?>" type="radio">
                            <?=$value?>
                        </label>
                    <? endforeach;?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Специальность</label>
                <div class="col-md-8">
                    <select id="specialtyList" name="Application[specialtyId]" class="form-control" onChange="SpecialtyIdChangeValue()">
                        <?php foreach ($organizationData['specialties'] as $key => $value):?>
                            <option value="<?=$key?>"><?=$value['title']?> (<?=$value['title']?>)</option>
                        <? endforeach;?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Форма обучения</label>
                <div class="col-md-8">
                    <select id="studyModeList" name="Application[studyMode]" class="form-control"></select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Базовое образование</label>
                <div class="col-md-8">
                    <select id="baseEducationList" name="Application[baseEducation]" class="form-control"></select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Необходимо место в общежитии</label>
                <div class="col-md-8">
                    <div class="checkbox">
                        <label for="checkboxes">
                            <input name="Application[applicationNeedHostel]" value="1" type="checkbox">
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-4 col-md-8">
                    <a class="btn btn-info" href="<?=AbiturientOfficeUrlHelper::getApplicationListUrl()?>">Вернуться к списку заявлений</a>
                    <button class="btn btn-success" type="submit">Подать заявление</button>
                </div>
            </div>

        </fieldset>
    </form>
    <?php endif; ?>
    </div>
</div>


<script type="text/javascript">
<?php echo 'var specialties = ' . CUtil::PhpToJSObject($arResult['organizationWithAvailableSpecialties']['specialties']);?>

	SpecialtyIdChangeValue();

	function SpecialtyIdChangeValue()
	{
		var specialtySelect = document.getElementById("specialtyList");
		var studyModeSelect = document.getElementById("studyModeList");
		var baseEducationSelect = document.getElementById("baseEducationList");

		studyModeSelect.options.length = 0;
		baseEducationSelect.options.length = 0;

		var selectedSpecialty = specialtySelect.options[specialtySelect.selectedIndex].value;


		for (var key in specialties[selectedSpecialty]["baseEducation"])
		{
			baseEducationSelect.options[baseEducationSelect.options.length] = new Option(specialties[selectedSpecialty]["baseEducation"][key], key);
		}

		for (var key in specialties[selectedSpecialty]["studyMode"])
		{
			studyModeSelect.options[studyModeSelect.options.length] = new Option(specialties[selectedSpecialty]["studyMode"][key], key);
		}
	}

</script>

<script type="text/javascript">
	function dump(obj) {
		var out = "";
		if(obj && typeof(obj) == "object"){
			for (var i in obj) {
				out += i + ": " + obj[i] + "\n";
			}
		} else {
			out = obj;
		}
		alert(out);
	}
</script>

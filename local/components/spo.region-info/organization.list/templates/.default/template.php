<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Helpers\OrganizationInfoUrlHelper;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\TrainingType;
use Spo\Site\Dictionaries\AdaptationType;

?>
<style>
    input.poisk{
        width: 100%;
        margin-bottom: 10px;
        padding-left: 15px;
        font-size: 16px;
    }
</style>
<input type="text" placeholder="Поиск" class="poisk" onkeydown="NewPoicKlic.onclickk(this,event.keyCode);">
<script>
    function PoicKlic(){
        this.onclickk=function(a,key){
            if(key==13){
                document.location.href="?search="+a.value;
            }
        }
    }
    var NewPoicKlic=new PoicKlic();
</script>
<div class="blog organisation-list-search-form">
	<div class="blog-header" data-toggle="collapse" href="#searchForm">
		<strong>
			<i class="fa fa-search"></i> Поиск
		</strong>
	</div>
	<div class="blog-body collapse" id="searchForm">

		<form class="form-horizontal">
			<fieldset>
				<div class="form-group">
					<label class="col-md-4 control-label" for="name">Наименование учебного заведения</label>
					<div class="col-md-8">
						<input id="name" name="organizationFilter[name]" placeholder="" class="form-control input-md" type="text">
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4 control-label" for="selectbasic">Город</label>
					<div class="col-md-8">
						<select id="selectbasic" name="organizationFilter[city]" class="form-control">
							<option value="">Любой</option>
							<?php foreach($arResult['citiesList'] as $city):?>
							<option value="<?=$city['id']?>"><?=$city['name']?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4 control-label" for="specialty">Специальности подготовки (ФГОС)</label>
					<div class="col-md-8">
						<select id="specialty[]" name="organizationFilter[specialty][]" class="form-control" multiple="multiple">
							<?php foreach($arResult['specialtiesList'] as $specialty):?>
								<option value="<?=$specialty['id']?>"><?=$specialty['code']?> - <?=$specialty['title']?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-4 control-label" for="selectbasic">Форма обучения</label>
					<div class="col-md-8">
						<select id="selectbasic" name="organizationFilter[studyMode]" class="form-control">
							<option value="">Любая</option>
							<?php foreach(StudyMode::getValuesArray() as $key => $value):?>
								<option value="<?=$key?>"><?=$value?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">Программы подготовки на базе</label>
                    <div class="col-md-8">
                        <select id="selectbaseeducation" name="organizationFilter[baseEducation]" class="form-control">
                            <option value="">Любая</option>
                            <?php foreach(BaseEducation::getValuesArray() as $key => $value):?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">Программа обучения</label>
                    <div class="col-md-8">
                        <select name="organizationFilter[trainingType]" class="form-control">
                            <option value="">Любая</option>
                            <?php foreach(TrainingType::getValuesArray() as $key => $value):?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">Адаптация для лиц с ОВЗ</label>
                    <div class="col-md-8">
                        <select name="organizationFilter[adaptationType]" class="form-control">
                            <option value="">Не имеет значения</option>
                            <option value="any">Да</option>
                            <?php foreach(AdaptationType::getValuesArray() as $key => $value):?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

				<div class="form-group">
					<div class="col-md-8 pull-right">
						<button class="btn btn-info" type="submit">Поиск</button>
						<button class="btn btn-default" type="reset">Сбросить</button>
					</div>
				</div>

			</fieldset>
		</form>

	</div>
</div>

<div class="organisation-list">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Адрес</th>
                <th>Телефон</th>
                <th>Сайт</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($arResult['organizations'] as $organization): ?>
            <tr>
                <td class="title"><a href="<?= OrganizationInfoUrlHelper::getOrganizationMainPageUrl($organization['id'])?>"><i class="fa fa-graduation-cap"></i><span><?= $organization['name']?></span></a></td>
                <td class="address"><?= 'г. ' . $organization['city'] . ', ' . $organization['address'] ?></td>
                <td class="phone"><?= $organization['phone']?></td>
                <?php if (!empty($organization['site']))
                    $organization['site'] = '<a href="' . $organization['site'] . '">' . $organization['site'] . '</a>';
                ?>
                <td class="url"><?= $organization['site']?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

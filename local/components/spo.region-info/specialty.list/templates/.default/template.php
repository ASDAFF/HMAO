<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php use Spo\Site\Helpers\RegionInfoUrlHelper;?>
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

<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label class="col-md-4 control-label" for="specialty">Специальности подготовки (ФГОС):</label>
            <div class="col-md-8">
                <select id="specialty[]" name="organizationFilter[specialty][]" class="form-control" multiple="multiple">
                    <?php foreach($arResult['specialyties'] as $specialty):?>
                        <option value="<?=$specialty['id']?>"><?=$specialty['code']?> - <?=$specialty['title']?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group" >
            <div class="col-md-8 pull-right">
                <button class="btn btn-info" type="submit">Поиск</button>
                <button class="btn btn-default" type="reset" onclick="document.location.href='?'">Сбросить</button>
            </div>
        </div>
    </fieldset>
</form>
<div class="row specialty-list">
	<div class="col-md-12">

        <table class="table table-hover">
            <thead>
                <th>Код специальности (ФГОС)</th>
                <th>Наименование</th>
                <th></th>
            </thead>
        <tbody>
        <?php foreach ($arResult['specialtiesList'] as $specialtyGroup): ?>
            <tr class="info">
                <th colspan="3"><?=$specialtyGroup['groupTitle']?></th>
            </tr>
            <?php foreach($specialtyGroup['specialties'] as $specialty):?>
            <tr>
                <td class="code"><?=$specialty['code']?></td>
                <td class="title"><a href="<?=RegionInfoUrlHelper::getSpecialtyInfoUrl(array('specialtyId' => $specialty['id']))?>"><?=$specialty['title']?></a></td>
                <td class="find">
                    <a href="<?=RegionInfoUrlHelper::getOrganizationListUrl(array('organizationFilter[specialty][]' => $specialty['id']))?>">
                        подобрать учебное заведение
                        <i class="fa fa-share"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach;?>
        <?php endforeach; ?>
        </tbody>
        </table>

    </div>
</div>



<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php

use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\StudyMode;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Helpers\DateFormatHelper;

$data = $arResult['Renrollment'];
$spec = $arResult['spec'];
$info = $arResult['infos'];

?>

<div class="page-header">
    <h3>Список лиц, рекомендованных к зачислению</h3>
</div>
<form method="get" id="fillt">
Списки по специальностям (профессиям)
    <select name="spec" onchange="$('#fillt').submit()">
        <option value="" <?if(empty($_GET['spec']) || isset($_GET['spec'])) echo "selected=selected" ?>> Выберите специальность (профессию) </option>
        <?//TODO: Вставить проверку на то что есть бюджет или контракт
        foreach ($spec as $item):?>
            <?if($item['Grant']>0):?>
                <option value="<?=$item['Idspec']?>_1" <?if($_GET['spec']==$item['Idspec'].'_1') echo "selected=selected"?>> <?=$item['name'];?> <?=BaseEducation::getshortValues($item['baseEducation'])?> (Бюджетное финансирование) </option>
            <?endif;?>
            <?if($item['Tution']>0):?>
                <option value="<?=$item['Idspec']?>_2" <?if($_GET['spec']==$item['Idspec'].'_2') echo "selected=selected"?>> <?=$item['name'];?> <?=BaseEducation::getshortValues($item['baseEducation'])?> (Контрактное финансирование) </option>
            <?endif;?>
        <?endforeach;?>
    </select>
<?if(!empty($data)):?>    
    <h3> <?=$info;?> </h3>
    <table width="100%" class="table table-bordered">
        <tr>
            <th>№ п.п.</th>
            <th>ФИО абитуриента</th>
            <th>Оригинал документа об образовании</th>
            <th>Средний балл аттестата</th>
            <th>Приоритет</th>
        </tr>
        <?php
        $i=0;
        foreach($data as $d) {
            $i++;
            ?>
            <tr <?if($d['ENROLLMENT']) echo "class='green'"?>>
                <td><?=$i?></td>
                <td><?=$d['ENROLLMENT_FIO']?></td>
                <td><?=$d['ENROLLMENT_COPY']==1 ? 'Оригинал' : 'Копия'?></td>
                <td><?=$d['ENROLLMENT_BALL']?></td>
                <td><?=$d['ENROLLMENT_PRIORY']?></td>
            </tr>
        <?php } ?>
    </table>
<?endif;?>





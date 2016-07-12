<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
$users = $arResult['users'];
$abiturientExams = $arResult['abiturientExams'];
?>
<? if(!empty($abiturientExams)): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Абитуриент</th>
                <th>Предмет</th>
                <th>Форма проведения</th>
                <th>Дата</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <? foreach($abiturientExams as $exam): ?>
            <tr>
                <td><?= $users[$exam['ID_ABITURIENT']] ?></td>
                <td><?= (trim($exam['TEST'])) ?></td>
                <td><?= $exam['FROM_EXEM'] ?></td>
                <td><?= $exam['DATE'] ?></td>
                <? if($exam['APPEAR']): ?>
                    <? if($exam['BALL'] > 0): ?>
                        <td>Сдан</td>
                    <? else: ?>
                        <td>Не сдан</td>
                    <? endif; ?>
                <? else: ?>
                    <td>Неявка</td>
                <? endif; ?>
            </tr>
        <? endforeach; ?>
        </tbody>
    </table>
<? endif; ?>
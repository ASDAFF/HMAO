<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
use Spo\Site\Dictionaries\ExamDiscipline;
use Spo\Site\Dictionaries\ExamType;
use Spo\Site\Dictionaries\BaseEducation;
use Spo\Site\Dictionaries\StudyMode;

$specialities = $arResult['specialities'];
$exams = $arResult['exams'];
?>
<? foreach($specialities as $orgSpecialityId => $speciality): ?>
    <? if(!empty($exams[$orgSpecialityId])): ?>
        <table class="table table-bordered">
            <caption>
                <?= $speciality['NAME'] ?> (<?= $speciality['CODE'] ?>)
                <span class="pull-right small">
                    <?= BaseEducation::getshortValues($speciality['BASE_EDUCATION']).', '.StudyMode::getshortValues($speciality['STUDY_MODE']) ?>
                </span>
            </caption>
            <thead>
                <tr>
                    <th>Экзамен</th>
                    <th>Форма проведения</th>
                    <th>Дата проведения</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($exams[$orgSpecialityId] as $exam): ?>
                    <tr>
                        <td><?= ExamDiscipline::getValue($exam['ORGANIZATION_SPECIALTY_EXAM_DISCIPLINE']) ?></td>
                        <td><?= ExamType::getValue($exam['ORGANIZATION_SPECIALTY_EXAM_TYPE']) ?></td>
                        <td><?= $exam['DATE'] ?></td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
    <? endif; ?>
<? endforeach; ?>

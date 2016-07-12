<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var $APPLICATION
 * @var $arResult
 */
$planData = $arResult['planData'];
?>
<style>
    .org-title{vertical-align: middle !important;}
    .org-title > div{transform: rotate(-90deg);}
    .cell-no-wrap{white-space: nowrap;}
</style>
<div class="department-dashboard">

    <div class="panel panel-primary">
        <table class="table table-hover">
            <tr>
                <th>Орг</th>
                <th>Специальность</th>
                <th>Код</th>
                <th>Баз обр</th>
                <th>Форма об.</th>
                <th>Уровень</th>
                <th>Месяцев</th>
                <th>Квал</th>
                <th>Бюдж</th>
                <th>Бюдж гр</th>
                <th>Плат</th>
                <th>Плат гр</th>
            </tr>
            <?foreach($planData as $organizationId => $organizationPlan){
                $str = '<tr>';
                $str .= '<td class="org-title" rowspan="' . max($organizationPlan['orgSpecCnt'],1) . '"><div>' . $organizationPlan['organizationName'] . '</div></td>';


                foreach($organizationPlan['specialties'] as $specId=>$specialty){
                    if(empty($str)){$str = '<tr>';}
                    $str .= '<td rowspan="' . max(count($specialty['organizationSpecialties']),1) . '">' . $specialty['specialtyTitle'] . '</td>' .
                            '<td rowspan="' . max(count($specialty['organizationSpecialties']),1) . '">' . $specialty['specialtyCode'] . '</td>';

                    foreach($specialty['organizationSpecialties'] as $orgSpecId=>$orgSpecialty){
                        if(empty($str)){$str = '<tr>';}
                        $str .= '<td>' . $orgSpecialty['baseEducation'] . '</td>' .
                                '<td>' . $orgSpecialty['studyMode'] . '</td>' .
                                '<td>' . $orgSpecialty['trainingLevel'] . '</td>' .
                                '<td>' . $orgSpecialty['studyPeriod'] . '</td>' .
                                '<td>' . $orgSpecialty['qualificationTitle'] . '</td>' .
                                '<td class="cell-no-wrap">' . $orgSpecialty['grantReqCount'] . ' из ' . $orgSpecialty['grantStudentsNumber'] . '</td>' .
                                '<td>' . $orgSpecialty['grantGroupsNumber'] . '</td>' .
                                '<td class="cell-no-wrap">' . $orgSpecialty['tuitionReqCount'] . ' из ' . $orgSpecialty['tuitionStudentsNumber'] . '</td>' .
                                '<td>' . $orgSpecialty['tuitionGroupsNumber'] . '</td>' .
                                '<td>' . $orgSpecialty['applicationFundingType'] . '</td>' .
                                '</tr>';

                        echo $str;
                        $str = '';
                    }
                }
            }?>
        </table>
    </div>

</div>
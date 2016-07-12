<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Spo\Site\Helpers\EduDepartmentOfficeUrlHelper as Url;
use Spo\Site\Dictionaries\AdmissionPlanStatus;

/**
 * @var $APPLICATION
 * @var $arResult
 */
$admissionPlansStat = $arResult['admissionPlansStat'];
$applicationsStat = $arResult['applicationsStat'];
?>
<div class="department-dashboard">

    <a href="<?=Url::toAdmissionPlanView()?>" class="btn btn-sm btn-default">
        <i class="fa fa-edit"></i> Контрольные цифры приема
    </a>

    <a href="<?=Url::toAdmissionPlanByOrganizationsView()?>" class="btn btn-sm btn-default">
        <i class="fa fa-edit"></i>Контрольные цифры приёма по организациям
    </a>

    <a href="<?=Url::toAdmissionPlanFactView()?>" class="btn btn-sm btn-default">
        <i class="fa fa-edit"></i>План-фактный анализ количества поданных заявлений к плану набора
    </a>

    <br />
    <br />

    <div class="panel panel-info">
        <div class="panel-heading">
            КЦП <?=date('Y')?> - Ханты-Мансийский автономный округ
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Всего специальностей:</strong> <?=$admissionPlansStat['specialtiesNumber']?></p>
                    <p><strong>Всего организаций:</strong> <?=$admissionPlansStat['organizationsNumber']?></p>
                    <p><strong> Бюджетных мест: </strong> <?=$admissionPlansStat['intramuralGrantStudentsNumber'] + $admissionPlansStat['extramuralGrantStudentsNumber']?></p>
                    <p><strong> Контрактных мест: </strong> <?=$admissionPlansStat['intramuralTuitionStudentsNumber'] + $admissionPlansStat['extramuralTuitionStudentsNumber']?></p>
                    <p class="text-danger">
                        <strong> Не утверждённые планы приёма: </strong>  <?=$admissionPlansStat['declinedAdmissionPlansNumber']?> <a href="<?= Url::toAdmissionPlanByOrganizationsView(array('filter[admissionPlanStatus]' => AdmissionPlanStatus::DECLINED))?>"> (просмотр)</a>
                    </p>
                    <p class="text-warning">
                        <strong> Не рассмотренные планы приёма: </strong>  <?=$admissionPlansStat['createdAdmissionPlansNumber']?> <a href="<?= Url::toAdmissionPlanByOrganizationsView(array('filter[admissionPlanStatus]' => AdmissionPlanStatus::CREATED))?>"> (просмотр)</a>
                    </p>
                </div>
                <div class="col-md-8">
                    <div id="chart_div"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <?=date('Y')?> - ход приёма заявлений абитуриентов
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Подано заявлений всего:</strong> <?=$applicationsStat['allApplicationsNumber']?></p>
                    <p>
                        <strong> ТОП-5 специальностей по количеству заявлений: </strong>
                        <ul>
                        <?php foreach ($applicationsStat['top5Specialties'] as $specialtyTitle => $applicationsNumber) { ?>
                            <li><?=$specialtyTitle?> <strong>(<?=$applicationsNumber?>)</strong></li>
                        <?php }?>
                        </ul>
                    </p>
                </div>
                <div class="col-md-8">
                    <div id="chart2_div"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    // Load the Visualization API and the piechart package.
    google.load('visualization', '1.0', {'packages':['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
            ['Очно, бюджет', <?=$admissionPlansStat['intramuralGrantStudentsNumber']?>],
            ['Заочно, бюджет', <?=$admissionPlansStat['extramuralGrantStudentsNumber']?>],
            ['Очно, контракт', <?=$admissionPlansStat['intramuralTuitionStudentsNumber']?>],
            ['Заочно, контракт', <?=$admissionPlansStat['extramuralTuitionStudentsNumber']?>]
        ]);

        // Set chart options
        var options = {
            title:'Распределение мест',
            is3D: true,
            width:650,
            height:350
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>

<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawVisualization);

    function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable([
            ['Месяц', 'Бюджет очно', 'Бюджет заочно', 'Контракт очно', 'Контракт заочно'],
            <?php foreach ($applicationsStat['applicationsByDate'] as $date => $a) { ?>
            ['<?= $date ?>', <?=$a['intramuralGrantApplicationsNumber']?>, <?=$a['extramuralGrantApplicationsNumber']?>, <?=$a['intramuralTuitionApplicationsNumber']?>, <?=$a['extramuralTuitionApplicationsNumber']?>],
            <?php } ?>
        ]);

        var options = {
//            title : 'Ход приёма заявлений',
            vAxis: {title: 'Заявлений'},
            hAxis: {title: 'Месяц'},
            seriesType: 'bars',
            series: {5: {type: 'line'}},
            height: 400,
            width: 700,
            legend: {
                position: 'top'
            }
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart2_div'));
        chart.draw(data, options);
    }
</script>
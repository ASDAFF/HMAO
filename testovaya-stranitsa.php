<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тестовая страница");
?> 
<div class="department-dashboard"> <a class="btn btn-sm btn-default" > <i class="fa fa-edit"></i> Контрольные цифры приема </a> <a class="btn btn-sm btn-default" > <i class="fa fa-edit"></i>Контрольные цифры приёма по организациям </a> <a class="btn btn-sm btn-default" > <i class="fa fa-edit"></i>План-фактный анализ количества поданных заявлений к плану набора </a> 
  <br />
 
  <br />
 
  <div class="panel panel-info"> 
    <div class="panel-heading"> КЦП 2015 - Ханты-Мансийский автономный округ </div>
   
    <div class="panel-body"> 
      <div class="row"> 
        <div class="col-md-4"> 
          <p><strong>Всего специальностей:</strong> 57</p>
         
          <p><strong>Всего организаций:</strong> 19</p>
         
          <p><strong> Бюджетных мест: </strong> 192</p>
         
          <p><strong> Внебюджетных мест: </strong> 201</p>
         
          <p class="text-danger"> <strong> Не утверждённые планы приёма: </strong> 21 <a href="#" > (просмотр)</a> </p>
         
          <p class="text-warning"> <strong> Не рассмотренные планы приёма: </strong> 13 <a href="#" > (просмотр)</a> </p>
         </div>
       
        <div class="col-md-8"> 
          <div id="chart_div"></div>
         </div>
       </div>
     </div>
   </div>
 
  <div class="panel panel-info"> 
    <div class="panel-heading"> 2015 - ход приёма заявлений абитуриентов </div>
   
    <div class="panel-body"> 
      <div class="row"> 
        <div class="col-md-4"> 
          <p><strong>Подано заявлений всего:</strong> 246</p>
         
          <p> <strong> ТОП-5 специальностей по количеству заявлений: </strong> </p>
         
          <ul> 
            <li>Гидрометнаблюдатель <strong>152</strong></li>
           
            <li>Гидрология<strong>144</strong></li>
           
            <li>Картография<strong>91</strong></li>
           
            <li>Монтажник трубопроводов<strong>87</strong></li>
           
            <li>Водоснабжение и водоотведение <strong>79</strong></li>
           </ul>
         
          <p></p>
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
            ['Очно, бюджет', 215],
            ['Заочно, бюджет', 190],
            ['Очно, внебюджет', 187],
            ['Заочно, внебюджет', 144]
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
            ['Месяц', 'Бюджет очно', 'Бюджет заочно', 'Внебюджет очно', 'Внебюджет заочно'],
            ['Июнь 01-15',  165,      938,         522,             998],
            ['Июнь 15-30',  135,      1120,        599,             1268],
            ['Июль 01-15',  157,      1167,        587,             807],
            ['Июль 15-30',  139,      1110,        615,             968],
            ['Август 01-15',  136,      691,         629,             1026],
            ['Август 15-30',  136,      691,         77,             1026]
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
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php $organization = $arResult['organization']?>

<div class="organization-page">

    <div class="row">
        <div class="col-md-3"><strong>Полное наименование</strong></div>
        <div class="col-md-9"><?=$organization['organizationFullName']?></div>
    </div>

    <div class="row">
        <div class="col-md-3"><strong>Адрес</strong></div>
        <div class="col-md-9"><?=$organization['organizationAddress']?></div>
    </div>

    <div class="row">
        <div class="col-md-3"><strong>Телефон</strong></div>
        <div class="col-md-9"><?=$organization['organizationPhone']?></div>
    </div>

    <?if ($organization['organizationEmail']) :?>
    <div class="row">
        <div class="col-md-3"><strong>E-mail</strong></div>
        <div class="col-md-9"><?=$organization['organizationEmail']?></div>
    </div>
    <?endif;?>

    <?if ($organization['organizationFoundationYear']) :?>
    <div class="row">
        <div class="col-md-3"><strong>Год основания</strong></div>
        <div class="col-md-9"><?=$organization['organizationFoundationYear']?></div>
    </div>
    <?endif;?>

    <?if ($organization['organizationSite']) :?>
    <div class="row">
        <div class="col-md-3"><strong>Сайт</strong></div>
        <div class="col-md-9"><a href="<?=$organization['organizationSite']?>"><?=$organization['organizationSite']?></a></div>
    </div>
    <?endif;?>

    <hr/>

</div>

<? if (($organization['organizationCoordinateX'] != '') && ($organization['organizationCoordinateX'] != '')) :?>
<div id="map"></div>

<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script>

    ymaps.ready(init);

    function init () {
        var myMap = new ymaps.Map("map", {
                center: [<?=$organization['organizationCoordinateX']?>, <?=$organization['organizationCoordinateY']?>],
                zoom: 16
            }),
            myPlacemark = new ymaps.Placemark([<?=$organization['organizationCoordinateX']?>, <?=$organization['organizationCoordinateY']?>], {
                // Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
                balloonContentBody: '<?=$organization['organizationName']?>'
            });

        myMap.geoObjects.add(myPlacemark);

    }

</script>
<? endif; ?>

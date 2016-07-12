<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    $mapData = $arResult['mapData'];
    //\Spo\Site\Util\CVarDumper::dump($mapData);
?>
<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script>
    var mapData = <?=json_encode($mapData)?>;
    regionMap = {};
    ymaps.ready(init);

    function init(){
        var i, mdItem, placemark;

        regionMap = new ymaps.Map('map-container', {
            controls: [
                'zoomControl',
                'fullscreenControl',
                'typeSelector' // слои
            ],
            center: [60.908839, 71.332164],
            zoom: 8
        });

        for(i=0;i<mapData.length;i++){
            mdItem = mapData[i];
            placemark = new ymaps.Placemark([mdItem.x, mdItem.y], {
                hintContent:    mdItem.name,
                balloonContent: [
                    '<a target="_blank" href="' + mdItem.url + '">',
                        mdItem.name,
                    '</a>'
                ].join('')
            });

            regionMap.geoObjects.add(placemark);
        }
    }
</script>

<div id="map-container"></div>

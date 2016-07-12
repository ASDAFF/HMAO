<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
    use \Bitrix\Main\Localization\Loc as Loc;
    Loc::loadMessages(__FILE__);
?>
<?
    $organizationData = $arResult['organizationData'];
    $availableEducationList = $arResult['availableEducationList'];
    $availableAdditionalLanguageList = $arResult['availableAdditionalLanguageList'];
    $regionAreasList = $arResult['regionAreasList'];
?>

<?php //TODO алерты можно вынести в header шаблона ?>

<?php/* if ($arResult['errors']):?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?php foreach($arResult['errors'] as $error): ?>
            <p><?=$error['code']?> - <?=$error['message']?> - <?=$error['invalidValue']?></p>
        <?php endforeach; ?>
    </div>
<?php endif; */?>

<div class="organisation-info-edit">

    <h2 class="page-header">Редактирование основной информации</h2>

    <?php if ($arResult['success']):?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?= $arResult['success'] ?>
        </div>
    <?php endif; ?>

    <form id="organization-info-form" class="form-horizontal" method="post">
        <fieldset>
            <div class="form-group">
                <label class="col-md-3 control-label" for="Organization[organizationName]">Краткое название организации</label>
                <div class="col-md-9">
                    <input name="Organization[organizationName]" class="form-control input-md" type="text" value="<?=htmlspecialchars($organizationData['organizationName'])?>">
                    <span class="error-list label label-danger"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="Organization[organizationFullName]">Полное название организации</label>
                <div class="col-md-9">
                    <textarea name="Organization[organizationFullName]" style="resize: vertical;" class="form-control input-md"><?=htmlspecialchars($organizationData['organizationFullName'])?></textarea>
                    <span class="error-list label label-danger"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="Organization[organizationFoundationYear]">Год основания</label>
                <div class="col-md-9">
                    <input name="Organization[organizationFoundationYear]"class="form-control input-md" type="text" value="<?=$organizationData['organizationFoundationYear']?>">
                    <span class="error-list label label-danger"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label">Адрес организации</label>
                <div class="col-md-9">
                    <input name="Organization[organizationAddress]" class="form-control input-md" type="text" value="<?=htmlspecialchars($organizationData['organizationAddress'])?>">
                    <span class="error-list label label-danger"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label">Email организации</label>
                <div class="col-md-9">
                    <input name="Organization[organizationEmail]" class="form-control input-md" type="text" value="<?=htmlspecialchars($organizationData['organizationEmail'])?>">
                    <span class="error-list label label-danger"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label">Телефон организации</label>
                <div class="col-md-9">
                    <input name="Organization[organizationPhone]" class="form-control input-md" type="text" value="<?=$organizationData['organizationPhone']?>">
                    <span class="error-list label label-danger"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label">Сайт организации</label>
                <div class="col-md-9">
                    <input name="Organization[organizationSite]" class="form-control input-md" type="text" value="<?=htmlspecialchars($organizationData['organizationSite'])?>">
                    <span class="error-list label label-danger"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="selectbasic">Район</label>
                <div class="col-md-9">
                    <select id="selectbasic" name="Organization[regionArea]" class="form-control">
                        <?php foreach($regionAreasList as $regionAreaData):?>
                            <option
                                <?=$organizationData['regionArea'] === $regionAreaData['regionAreaId'] ? 'selected="selected"' : ''?>
                                value="<?=$regionAreaData['regionAreaId']?>">
                                <?=$regionAreaData['regionAreaName']?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="selectbasic">Город</label>
                <div class="col-md-9">
                    <select id="selectbasic" name="Organization[city]" class="form-control">
                        <?php foreach($arResult['citiesList'] as $city):?>
                            <option
                                <?=$organizationData['cityId'] === $city['id'] ? 'selected="selected"' : ''?>
                                value="<?=$city['id']?>">
                                <?=$city['name']?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <input name="Organization[organizationCoordinateX]" type="hidden" value="<?=$organizationData['organizationCoordinateX']?>" data-last-x="<?=$organizationData['organizationCoordinateX']?>">
            <input name="Organization[organizationCoordinateY]" type="hidden" value="<?=$organizationData['organizationCoordinateY']?>" data-last-y="<?=$organizationData['organizationCoordinateY']?>">

            <div class="form-group">
                <label class="col-md-3 control-label">Местоположение</label>
                <div class="col-md-9">
                    <div id="map-container" style="height:600px;"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="selectbasic">Возможность предоставить общежитие</label>
                <div class="col-md-9">
                    <input type="checkbox" name="Organization[hostel]" value="1" <? if($organizationData['hostel']) echo "checked";?>>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-3 col-md-9">
                    <button class="btn btn-info" type="submit"><i class="fa fa-check"></i> Сохранить</button>
                    <a class="btn btn-default" href="/organization-office/">Отмена</a>
                </div>
            </div>

        </fieldset>
    </form>

</div>

<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script>
    map = {};
    ymaps.ready(init);


    function init(){
        var i,
            mdItem,
            scp = {
                placemark : {},
                $xFld     : $('input[name="Organization[organizationCoordinateX]"]'),
                $yFld     : $('input[name="Organization[organizationCoordinateY]"]'),
                $nameFld  : $('input[name="Organization[organizationName]"]')
            };

        scp.x = parseFloat(scp.$xFld.val());
        scp.y = parseFloat(scp.$yFld.val());

        map = new ymaps.Map('map-container', {
            controls: [
                      'default'
//                'zoomControl',
//                'fullscreenControl',
//                'typeSelector' // слои
            ],
            center: [scp.x, scp.y], // Москва
            zoom: 10
        });

        var searchControl = map.controls.get('searchControl')
        searchControl.events
//        .add('load', function (event) {
//            var sc = map.controls.get('searchControl');
//            // Проверяем, что это событие не "дозагрузки" результатов и
//            // по запросу найден хотя бы один результат.
////            if (!event.get('skip') && searchControl.getResultsCount()) {
////                searchControl.showResult(0);
////            }.get
//            //return false;
//        })
        .add('resultshow', function (event) {
            var sc     = map.controls.get('searchControl');
            var res    = sc.getResultsArray()[event.get('index')];
            var coords = res.geometry.getCoordinates();

            this.$xFld.val(coords[0]);
            this.$yFld.val(coords[1]);
            this.placemark.geometry.setCoordinates(coords);

            sc.hideResult();
        }, scp);

        scp.placemark = new ymaps.Placemark([scp.x, scp.y], {
            hintContent:    scp.$nameFld.val(),
            balloonContent: scp.$nameFld.val()
        });

        map.geoObjects.add(scp.placemark);

        map.events.add('click', function (e) {
            var coords = e.get('coords'); // Получение ссылки на объект, сгенерировавший событие (карта).
            //eMap.setType('yandex#hybrid');
            this.$xFld.val(coords[0]);
            this.$yFld.val(coords[1]);
            this.placemark.geometry.setCoordinates(coords);
        }, scp);

        var resetButton =
            new ymaps.control.Button({
                data: {
                    content: 'Сбросить позицию до последней сохраненной'
                },
                options: {
                    selectOnClick:  false,
                    size:          'large',
                    maxWidth:       350
                }
            });

        resetButton.events
            .add(
                'click',
                function () {
                    var coords = [
                        this.$xFld.data('last-x'),
                        this.$yFld.data('last-y')
                    ];
                    this.$xFld.val(coords[0]);
                    this.$yFld.val(coords[1]);
                    this.placemark.geometry.setCoordinates(coords);
                },
                scp
            );

        map.controls.add(resetButton, {
            //float: "left",
            position: {
                bottom: 10,
                left: 10
            }
        });
    }

    $(function(){
        var errorList = <?=json_encode(isset($arResult['errors']) ? $arResult['errors'] : array());?>,
            orgForm   = new SpoForm($('form#organization-info-form'));

        orgForm.showValidationErrors(errorList);
    });

</script>
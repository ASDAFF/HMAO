<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?php
use Spo\Site\Helpers\AbiturientOfficeUrlHelper;
use Spo\Site\Dictionaries\TrainingLevel;
use Spo\Site\Dictionaries\ApplicationStatus;
use Spo\Site\Dictionaries\ApplicationPriority;
?>
<script>
    $(function() {
        <?
        foreach ($arResult['userApplications'] as $key=>$application):
        ?>
        $("#example<?=$key;?>").sortable({
            /*onDrop: function($item, container, _super) {
                var mass = $('#example<?=$key;?> ul.example li .unsortable .prior');
                for(var i=0; i<mass.length; i++){
                    mass.eq(i).val(i+1);
                }
            }*/
        });

        <?endforeach; ?>
        <?
        $resultList=$arResult['userApplications'];
        if (count($resultList)>1)
        {
            $keys=array_keys($resultList);
            $str='';
            foreach ($keys as $k=>$kk)
            {
                $str.='.example'.$kk.' li';
                if ($k<count($kk))
                {
                    $str.=', ';
                }
            }
          //  echo '$( "'.$str.'" ).disableSelection()';
          //  echo '$( "'.$str.'" ).disableSelection()';
        }
        ?>

    });
    //$(".example").disableSelection();
    // $(".example").draggable({ containment:"#exm1", scroll:false });
</script>

<?php if (!empty($arResult['userApplications'])):?>
<form method="post" id="App">
    <?php $j=0;
    foreach ($arResult['userApplications'] as $key=>$applications):
        ?>
        <h2> <?=$applications['name']?> </h2>
        <ul id="example<?=$key?>" class="containment-wrapper application-list example">
            <?php foreach ($applications['items'] as $k=>$application):
                $j++;
                ?>
                <? switch ($application['statusCode']) {
                    case ApplicationStatus::CREATED: $panelClass = 'primary'; break;
                    case ApplicationStatus::ACCEPTED: $panelClass = 'success'; break;
                    case ApplicationStatus::RETURNED: $panelClass = 'warning'; break;
                    case ApplicationStatus::DECLINED: $panelClass = 'danger'; break;
                    case ApplicationStatus::DELETED: $panelClass = 'danger'; break;
                }?>
                <li class="panel panel-<?=$panelClass;?>" data-id="" data-name="<?= $application['id'] ?>" id="<?=$application['id']?>">
                    <div class="panel-heading unsortable">
                        <span class="status"><?= $application['status']?></span>
                        <strong>Заявка №<?= $application['id'] ?></strong> от <?= $application['creationDate'] ?>
                    </div>
                    <div class="panel-body unsortable">
                        <p>
                            Подано в <strong><?= $application['organizationName'] ?></strong>
                            по&nbsp;специальности <strong><?= $application['specialtyTitle'] ?> (<?= $application['specialtyCode'] ?>)</strong>
                        </p>
                        <p>
                            Форма обучения: <strong><?= $application['studyMode'] ?></strong><br/>
                            Длительность обучения: <strong><?= ($application['studyPeriod'] - $application['studyPeriod']%12)/12;?> г.  <?=$application['studyPeriod']%12?> м.</strong><br/>
                        </p>
                        <p>
                            Уровень подготовки: <strong><?= TrainingLevel::getValue($application['trainingLevel']) ?></strong><br/>
                            Базовое образование: <strong><?= $application['baseEducation'] ?></strong>
                        </p>
                        <p>
                            Форма финансирования: <strong><?= $application['applicationFundingType'] ?></strong>
                        </p>
                        <p>Общежитие <?= $application['needHostel'] ? '<strong>требуется</strong>' : 'не требуется' ?></p>

                        <div class="buttons pull-right">
                            <a class="btn icon-move btn btn-info btn-sm Move"><i class="fa fa-arrows-alt fa-lg"></i>Переместить</a>
                            
                            <?if($application['statusCode']==1):?><a href="<?= AbiturientOfficeUrlHelper::getApplicationEditUrl($application['id'])?>" class="btn btn-info btn-sm"><i class="fa fa-check"></i> Редактировать</a> <? endif; ?>
                        </div>
                        <div class="priority">Приоритет: <span><?=ApplicationPriority::getValue($application['priority'])?></span></div>

                    </div>
                    <input type="hidden" name="AppPrior[<?=$application['id']?>][res]" data="<?=$key?>" class="prior" value="<?=$application['priority']?>" id="<?=$j?>">
                    <input type="hidden" name="AppPrior[<?=$application['id']?>][bul]" data="<?=$key?>" class="prior_bul" value="0" id="bul_<?=$j?>">
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
    </form>
<?php else:?>
    <p class="text-info">Вы ещё не подали ни одной заявки на поступление.</p>
<?php endif;?>
<button href="#" class="btn btn-info btn-sm saveprior" onclick="savepriority();">Сохранить</button>
<p class="infprior"><i class="fa fa-asterisk"></i>Для изменения приоритета необходимо зажать кнопку "Переместить" и перетащить заявку. Заявки с большим приоритетом будут находиться сверху.</p>
<button href="#" class="btn btn-info btn-sm changeprior" onclick="changepriority()">Изменить приоритет</button>




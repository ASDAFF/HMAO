<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/* @var array $arParams */
//var_dump($arParams);
$pageCount       = $arParams['PageCount'];
$totalCount      = $arParams['TotalCount'];
$currentPage     = $arParams['CurrentPage'];
$shownPages      = $arParams['ShownPages'];
$shownTotalCount = $arParams['ShownTotalCount'];
$navClass        = $arParams['NavClass'];
$limit           = $arParams['Limit'];
$randCls         = 'rnd-cls' . rand(); // для возможности использовать несколько пейджингов на странице


//$pages = array();

$p = ceil($totalCount/$limit);
$pages=(int) $p;

/*while(count($pages) <= $shownPages){
    if($i > 0){
        $pages[] = $i;
    }
    $i++;
    if($i > $pageCount){
        break;
    }
}*/
?>
<nav class="<?=$navClass ?> <?=$randCls?>">
    <ul class="pagination">
        <?
            if(empty($_GET['page']) || $_GET['page']>1):
        ?>
        <li>
            <a data-page="first" href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?
            endif;
        ?>
        <?for($i=1;$i<=$pages; $i++){?>
            <li class="<?=($i === $currentPage) ? 'active' : ''?>">
                <a data-page="<?=$i?>" href="#"><?=$i?></a>
            </li>
        <?}?>
        <?
            if($_GET['page']!=$pages):
        ?>
            <li>
                <a data-page="<?=$_GET['page']+1;?>" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <?
        endif;
        ?>
        <?if($shownTotalCount === 'Y'){?>
            <li style="line-height: 34px; padding-left: 10px;">
                Всего записей: <?=$totalCount?>
            </li>
        <?}?>
    </ul>
</nav>
<script>
    spoPaging.pageCount = <?=$pageCount?>;
    $(function(){
        $('nav.<?=$randCls?> ul.pagination a').each(function(index, el){
            var $el = $(el),
                page = $el.data('page'),
                lnk;
            switch(page){
                case 'first':
                    lnk = spoPaging.urlToFirstPage();
                    break;
                case 'last':
                    lnk = spoPaging.urlToLastPage();
                    break;
                default:
                    lnk = spoPaging.urlToPage(page);
                    break;
            }
            $el.attr('href', lnk);
        });
    });
</script>
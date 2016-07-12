<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//use Bitrix\Main\Loader;
//use Spo\Site\Util\CVarDumper;
//Loader::includeModule('spo.site');

$lastItem = 0;
$menu = array();
if(!empty($arResult))
{
    foreach($arResult as $index => $menuItem)
    {
        if($menuItem['DEPTH_LEVEL'] === 1)
        {
            $lastItem = $index;
            $menu[$lastItem] = array(
                'text'     => $menuItem['TEXT'],
                'link'     => $menuItem['LINK'],
                'selected' => $menuItem['SELECTED'],
                'items'    => array(),
            );
        }
        elseif($menuItem['DEPTH_LEVEL'] === 2)
        {
            $menu[$lastItem]['items'][] = array(
                'text'     => $menuItem['TEXT'],
                'link'     => $menuItem['LINK'],
                'selected' => $menuItem['SELECTED'],
            );
        }

    }
}
//CVarDumper::dump($menu);
?>

<?if (!empty($arResult)){?>
    <nav class="spo-horozontal-menu navbar navbar-default spo-mainmenu">
        <ul class="nav navbar-nav">
            <?foreach($menu as $menuItem){?>
                <?if(count($menuItem['items']) === 0){?>
                    <li class="<?= $menuItem['selected'] ? 'active' : ''?>">
                        <a href="<?=$menuItem['link']?>"><?=$menuItem['text']?></a>
                    </li>
                <?}else{?>
                    <li class="dropdown <?= $menuItem['selected'] ? 'active' : ''?>">
                        <a href="<?=$menuItem['link']?>" role="button" aria-expanded="false"><?=$menuItem['text']?><span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <?foreach($menuItem['items'] as $subMenuItem){?>
                                <li class="<?= $subMenuItem['selected'] ? 'active' : ''?>"><a href="<?=$subMenuItem['link']?>"><?=$subMenuItem['text']?></a></li>
                            <?}?>
                        </ul>
                    </li>
                <?}?>
            <?}?>
        </ul>
    </nav>
<?}?>
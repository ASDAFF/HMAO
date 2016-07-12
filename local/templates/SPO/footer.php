<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();
global $APPLICATION;
use Spo\Site\Domains\UserDomain;
use \Bitrix\Main\Loader;
use Spo\Site\Util\UiMessage;

Loader::includeModule('spo.site');
global $USER;

$currentUserId = $USER->GetID();

if (!empty($currentUserId) && UserDomain::checkIsAbiturient($USER->GetUserGroupArray())) {
    $userDomain = UserDomain::loadByUserId($currentUserId);
    $needProfileDataConfirmation = !$userDomain->isUserDataConfirmed();

    if ($needProfileDataConfirmation) {
        // Чтобы при последущих действиях сущности подгружались из базы
        //D::$em->clear();
        UiMessage::addMessage(
            'Необходимо <a href="/user-confirmation/confirm/">подтвердить</a> ваши регистрационные данные.',
            UiMessage::TYPE_WARNING
        );
    }
}?>
                        <?$APPLICATION->IncludeComponent("spo.ui-message", "", array('container-selector' => '#ui-message-panel'));?>
                        <?if($isIndexPage){?>
                            </div>
                        <?}?>
                    </div>
                </div>
            <div class="footer-spacer"></div>
        </div>
        <footer class="footer">
            <div class="container">
                <div class="footer-nav"></div>
                <div class="spo-copyright"></div>
                <div class="footer-links"></div>
                <div class="copyright">
                    <? $APPLICATION->IncludeFile(SITE_DIR."local/includes/copyright.php", Array(), Array("MODE"=>"html")); ?>
                </div>
            </div>
        </footer>
    </body>
</html>

<?php
// Если в arAdditionalChain пусто, то добавляем к пунктам навигационной цепочки title страницы. В противном случае,
// навигационная цепочка была модифицирована компонентом, и добавлять title страницы не нужно
if (empty($APPLICATION->arAdditionalChain))
    $APPLICATION->AddChainItem($APPLICATION->GetTitle());
?>
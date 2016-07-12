<?php
/**
 * Обёртка для класса CUser
 */

namespace Spo\Site\Util;

use CUser;

class CmsUser
{
    protected $user = null;
    public function __construct(CUser $user)
    {
        $this->user = $user;
    }

    public static function getCurrentUser()
    {
        global $USER;
        return new static($USER);
    }

    public function getId()
    {
        return intval($this->user->GetID());
    }

    public function isAuthorized()
    {
        return $this->user->IsAuthorized();
    }
}
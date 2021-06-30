<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class StatusConst extends AbstractConstants
{
    /**
     * @Message("Need Login")
     */
    const NEED_LOGIN = 999;

    /**
     * @Message("No Token")
     */
    const NO_TOKEN = 998;

    /**
     * @Message("login failed")
     */
    const LOGIN_FAIL = 700;
}

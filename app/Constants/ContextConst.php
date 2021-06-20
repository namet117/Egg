<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ContextConst extends AbstractConstants
{
    /**
     * @Message("User Token")
     */
    const AUTH_TOKEN = 'user_auth_token';

    /**
     * @Message("User Token Info")
     */
    const AUTH_INFO = 'user_auth_info';
}

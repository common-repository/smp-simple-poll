<?php

/**
 * @package SmppSimplePoll
 */

namespace Includes\Base;

class Deactivate
{
    public static function smpp_deactivate()
    {
        flush_rewrite_rules();
    }
}

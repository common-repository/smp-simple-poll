<?php

/**
 * @package SmppSimplePoll
 */

namespace Includes\Base;

class Activate
{
    public static function smpp_activate()
    {
        flush_rewrite_rules();
    }
}

<?php

/**
 * @package SmppSimplePoll
 */

namespace Includes;

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array full list of classes
     */

    public static function get_services()
    {
        return [
            Base\Enqueue::class,
            Base\TextDomain::class,
            Classes\SMPP_CPT::class,
            Classes\Poll_Metaboxes::class,
            Classes\Poll_Shortcode::class,
            Classes\Poll_Methodes::class,
        ];
    }

    /**
     * Initialize the class
     * @param class $class, class from the service array
     * @return class instance
     */

    public static function instantiate($class)
    {
        $service = new $class();

        return $service;
    }

    /**
     * Loop through the classes, intialize theme
     * and call the register method if it exits
     * @return nothing
     */
    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);

            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }
}

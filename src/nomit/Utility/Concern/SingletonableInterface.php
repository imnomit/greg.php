<?php

namespace nomit\Utility\Concern\Singletonable;

interface SingletonableInterface
{

    /**
     * @return static
     */
    public static function singleton();

}
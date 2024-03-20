<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function slugify($string)
    {
        $string = preg_replace("/ +/", "-", trim($string));
        $string = mb_strtolower(preg_replace('/[^A-Za-z0-9-]+/', '', $string), 'UTF-8');
        return $string;
    }
}

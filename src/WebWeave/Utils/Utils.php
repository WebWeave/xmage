<?php

namespace WebWeave\Utils;

use Symfony\Component\Finder\Finder;

class Utils
{
    public function getAllModules()
    {
        $finder = new Finder();
        $prefix = 'app/code';

        $dirs = $finder->directories()->depth(1)->in($prefix);

        $modules = array();

        foreach($dirs as $dir) {
            //Format for Magento's sakes
            $modules[] = str_replace('/', '_', $dir->getRelativePathname());
        }

        return $modules;
    }
}
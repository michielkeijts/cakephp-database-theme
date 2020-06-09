<?php
/* 
 * @copyright (C) 2020 Michiel Keijts, Normit
 * 
 */

namespace CakeDatabaseThemes\Helper;

use Cake\Filesystem\Folder;

class DatabaseThemeHelper {
    
    public static function getTemplatesByDirectory(string $directory = ""): array
    {
        $directory = ROOT . DS . $directory;
        
        $folder = new Folder($directory);
        
        foreach ($folder->findRecursive('*.php') as $file) {
            
        }
    }
}
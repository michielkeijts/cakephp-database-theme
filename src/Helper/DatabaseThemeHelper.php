<?php
/* 
 * @copyright (C) 2020 Michiel Keijts, Normit
 * 
 */

namespace CakeDatabaseThemes\Helper;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Core\Configure;

class DatabaseThemeHelper {
    
    /**
     * Get all the template in the Application
     * @param string $directory
     * @return array
     */
    public static function getTemplatesByDirectory(string $directory = ""): array
    {
        $templatePath = Configure::read('App.paths.templates');
        $directory = ROOT . DS . 'templates' . DS . $directory;
        
        $folder = new Folder($directory);
        
        $files = $folder->findRecursive('.*\.php');
        
        $list = [];
        foreach ($folder->findRecursive('.*\.php') as $file) {
            $list[] = str_replace($templatePath, '', $file);
        }
        
        return $list;
    }
    
    /**
     * Write the template value to a file
     * @param string $path
     * @param string $value
     * @return bool
     */
    public static function saveTemplate(string $path, string $value): bool
    {
        $path = sprintf('%s%s', Configure::read('CakeDatabaseThemes.pluginDir'), $path);
        
        $file = new File($path);
        $file->create();
        $file->write($value, 'w', true);
        
        return $file->close();
    }
}
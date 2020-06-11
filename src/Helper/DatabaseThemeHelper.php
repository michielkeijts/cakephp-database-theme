<?php
/* 
 * @copyright (C) 2020 Michiel Keijts, Normit
 * 
 */

namespace CakeDatabaseThemes\Helper;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Core\Configure;
use CakeDatabaseThemes\Model\Entity\Template;
use CakeDatabaseThemes\Model\Entity\Theme;

class DatabaseThemeHelper {
    
    /**
     * Get all the template in the Application
     * @param string $directory
     * @return array
     */
    public static function getTemplatesByDirectory(string $directory = ""): array
    {
        $templatePaths = Configure::read('App.paths.templates');
        
        foreach ($templatePaths as $templatePath) {
            $directory = $templatePath . trim($directory, DS);

            if (!file_exists($directory)) {
                continue;
            }
            
            $folder = new Folder($directory);

            $files = $folder->findRecursive('.*\.php');

            $list = [];
            foreach ($folder->findRecursive('.*\.php') as $file) {
                $list[] = str_replace($templatePath, '', $file);
            }

            return $list;
        }
    }
    
    /**
     * Get Template Entities, preloaded with data from the directory
     * @param string $directory
     * @return array
     */
    public static function getTemplateEntitiesByDirectory(string $directory = ""): array
    {
        $file_templates = static::getTemplatesByDirectory($directory);
        $templatePaths = Configure::read('App.paths.templates');
        
        $templates = [];
        foreach ($templatePaths as $templatePath) {
            foreach ($file_templates as $template) {
                $templates[] = new Template([
                    'name'  => $template,
                    'value' => file_get_contents($templatePath . $template)
                ]);
            }
        }
        
        return $templates;
    }
    
    /**
     * Write the template value to a file
     * @param string $path
     * @param string $value
     * @return bool
     */
    public static function saveTemplate(Theme $theme, string $path, string $value): bool
    {
        $path = sprintf('%s%s', $theme->getPath(), $path);
        
        $file = new File($path, true);
        $file->write($value, 'w', true);
        
        return $file->close();
    }
}
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
use Cake\Collection\Collection;

class DatabaseThemeHelper {
    
    /**
     * Get all the template in the Application
     * @param string $directory
     * @return array
     */
    public static function getTemplatesByDirectory(string $directory = ""): array
    {
        $templatePaths = Configure::read('App.paths.templates');
        $ignorePaths = Configure::read('CakeDatabaseThemes.ignoreTemplatePaths');
        
        foreach ($templatePaths as $templatePath) {
            $directory = $templatePath . trim($directory, DS);

            if (!file_exists($directory)) {
                continue;
            }
            
            $folder = new Folder($directory);

            $list = [];
            foreach ($folder->findRecursive('.*\.php') as $file) {
                $path = str_replace($templatePath, '', $file);
                
                if (static::isPathPrefixOfItems($path, $ignorePaths)) {
                    continue;
                }
                
                $list[] = $path;
            }

            return $list;
        }
    }
    
    /**
     * Checks if a $path is matched by a prefix defines by array $items
     * @param string $path
     * @param array $items
     * @return bool
     */
    private static function isPathPrefixOfItems(string $path, array $items): bool
    {
        $c = new Collection($items);
        $c = $c->filter(function($item) use ($path) {
            return strpos($path, $item) !== FALSE;
        });
        
        return $c->count() > 0;        
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
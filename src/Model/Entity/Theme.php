<?php
declare(strict_types=1);

namespace CakeDatabaseThemes\Model\Entity;

use Cake\ORM\Entity;
use Tools\Utility\Text;
use Cake\ORM\TableRegistry;
use CakeDatabaseThemes\Helper\DatabaseThemeHelper;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use CakeDatabaseThemes\Model\Entity\Template;

/**
 * CakeDatabaseThemesTheme Entity
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $parent_id
 * @property string|null $html_head
 * @property int $lft
 * @property int $rght
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 * @property \Cake\I18n\FrozenTime|null $deleted
 * @property int|null $deleted_by
 *
 * @property Theme $parent_theme
 * @property Theme[] $child_themes
 * @property Template[] $templates
 */
class Theme extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'parent_id' => true,
        'html_head' => true,
        'lft' => true,
        'rght' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'deleted' => true,
        'deleted_by' => true,
        'templates' => true,
        'parent_theme' => true,
        'child_themes' => true,
    ];
    
    /**
     * Convert `InputName with spaces%` to `Inputname-with-spaces`
     * @param type $name
     * @return string
     */
    protected function _setName($name): string
    {
        $name = ucfirst(strtolower(trim($name)));
        return Text::slug($name);
    }
    
    /**
     * Gets the path for the plugin
     * @param string $name
     * @return string
     */
    public function getPath(string $name = NULL): string
    {
        if (empty($name)) {
            $name = $this->name;
        }
        return sprintf('%s%s', Configure::read('CakeDatabaseThemes.pluginDir'), ucfirst(strtolower($name)));
    }
    
    /**
     * Get a HTML HEAD coalesced over all the parent themes
     * @return string
     */
    public function getHtmlHeadCoalesced(): string
    {
        $head = "";
        $parents = TableRegistry::getTableLocator()->get($this->getSource())->find('path', ['for' => $this->id]);
        foreach ($parents as $theme) {
            $head .= $theme->value;          
        }
        
        return $head;
    }
    
    /**
     * Get list of templates, default use the template directory
     * @return array
     */
    protected function _getTemplates($value): array
    {
        if ($this->name !== 'Default') {
            return $value ?: [];
        }
        
        $cacheCallback = function () {
            return DatabaseThemeHelper::getTemplateEntitiesByDirectory();;
        };
        
        return Cache::remember('CakeDatabaseThemeGetDefaultTemplates', $cacheCallback, Configure::read('CakeDatabaseThemes.cacheConfig'));
    }
    
    /**
     * Get list of templates coalesced for this Theme.
     * So templates from a child theme override the parent theme
     * @return Template[]
     */
    public function getTemplatesCoalesced(): array
    {
        $ancestors = TableRegistry::getTableLocator()->get($this->getSource())
                ->find('path', ['for' => $this->id])
                ->contain(['Templates'])
                ->toArray();
        
        $templates_coalesced = [];        
        foreach ($ancestors as $ancestor) {
            foreach ($ancestor->templates as $template) {
                $template->theme = ['id' => $ancestor->id, 'name' => $ancestor->name];
                $templates_coalesced[$template->name] = $template;
            }
        }
        
        return $templates_coalesced;
    }
}

<?php
declare(strict_types=1);

namespace CakeDatabaseThemes\Model\Entity;

use Cake\ORM\Entity;

/**
 * CakeDatabaseThemesTemplate Entity
 *
 * @property int $id
 * @property int $theme_id
 * @property string|null $name
 * @property string|null $value
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $created_by
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modified_by
 * @property \Cake\I18n\FrozenTime|null $deleted
 * @property int|null $deleted_by
 *
 * @property \CakeDatabaseTheme\Model\Entity\Theme $theme
 */
class Template extends Entity
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
        'theme_id' => true,
        'name' => true,
        'value' => true,
        'created' => true,
        'created_by' => true,
        'modified' => true,
        'modified_by' => true,
        'deleted' => true,
        'deleted_by' => true
    ];
    
    
}

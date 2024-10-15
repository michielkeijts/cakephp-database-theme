<?php
declare(strict_types=1);

namespace CakeDatabaseThemes\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use CakeDatabaseThemes\Model\Entity\Theme;
use CakeDatabaseThemes\Helper\DatabaseThemeHelper;

/**
 * CakeDatabaseThemesThemes Model
 *
 * @property \CakeDatabaseTheme\Model\Table\CakeDatabaseThemesThemesTable&\Cake\ORM\Association\BelongsTo $ParentCakeDatabaseThemesThemes
 * @property \CakeDatabaseTheme\Model\Table\CakeDatabaseThemesThemesTable&\Cake\ORM\Association\HasMany $ChildCakeDatabaseThemesThemes
 *
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme newEmptyEntity()
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme newEntity(array $data, array $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme[] newEntities(array $data, array $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme get($primaryKey, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTheme[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class ThemesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('cake_database_themes_themes');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->setEntityClass(Theme::class);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');

        $this->belongsTo('ParentThemes', [
            'className' => 'CakeDatabaseThemes.Themes',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('ChildThemes', [
            'className' => 'CakeDatabaseThemes.Themes',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('Templates', [
            'className' => 'CakeDatabaseThemes.Templates',
            'foreignKey' => 'theme_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('parent_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 45)
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('html_head')
            ->maxLength('html_head', 4294967295)
            ->allowEmptyString('html_head');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['name']));
        $rules->add($rules->existsIn(['parent_id'], 'ParentThemes'));

        return $rules;
    }

    /**
     * beforeDelete removes all the files for the plugin
     * @param EventInterface $event
     * @param EntityInterface $entity
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity)
    {
        if (!$this->removePlugin($entity)) {
            $event->stopPropagation();
        }
    }

    /**
     * Make sure the plugin dir exists
     * @param EventInterface $event
     * @param Theme $theme
     */
    public function beforeSave(EventInterface $event, Theme $theme)
    {
        if ($theme->isDirty('parent_id') && !empty($theme->getOriginal('parent_id'))) {
            $current_child_themes = $theme->child_themes?: [];

            $this->loadInto($theme, ['ChildThemes']);
            foreach (array_merge([$theme], $theme->child_themes ?: []) as $child_theme) {
                $this->removePlugin($child_theme);
            }

            $theme->original_child_themes = $theme->child_themes;
            $theme->child_themes = $current_child_themes;
        }
    }

    /**
     * Make sure the plugin dir exists
     * @param EventInterface $event
     * @param Theme $theme
     */
    public function afterSave(EventInterface $event, Theme $theme)
    {
        if ($theme->isDirty('name') && !$theme->isNew()) {
            $this->renameOrCreatePlugin($theme);
        } elseif ($theme->isDirty('name')) {
            $this->createPlugin($theme);
        }

        if ($theme->isDirty('parent_id')) {
            $this->replaceTemplatesForTheme($theme, TRUE);

            $this->loadInto($theme, ['ChildThemes']);
            $theme->original_child_themes = isset($this->original_child_themes) ?: [];
            foreach (($theme->original_child_themes + $theme->child_themes) as $child_theme) {
                $this->createPlugin($child_theme);
                $this->replaceTemplatesForTheme($child_theme);
            }
        }
    }

    /**
     * Update or create the template for a theme
     * @param Theme $theme
     * @param bool $force
     * @return bool
     */
    public function replaceTemplatesForTheme(Theme $theme, bool $force = FALSE): bool
    {
        foreach ($theme->getTemplatesCoalesced() as $template) {
            if ($template->theme->name === 'Default') {
                continue;
            }
            DatabaseThemeHelper::saveTemplate($theme, $template->name, $template->value);
        }
        return true;
    }

    /**
     * Creates a plugin (directory)
     * @param Theme $entity
     * @return bool
     */
    public function createPlugin(Theme $theme): bool
    {
        if (empty($theme->name)) {
            return FALSE;
        }

        if (file_exists($theme->getPath())) {
            return TRUE;
        }

        return mkdir($theme->getPath(), 0755, true);
    }

    /**
     * Removes a plugin (dir/files)
     * @param Theme $theme
     * @return bool
     */
    public function removePlugin(Theme $theme): bool
    {
        if (empty($theme->name)) {
            return FALSE;
        }

        if (!file_exists($theme->getPath())) {
            return TRUE;
        }

        return rmdir($theme->getPath());
    }

    /**
     * Renames a plugin (dir/files) if exists, or creates it
     * @param Theme $theme
     * @return bool
     */
    public function renameOrCreatePlugin(Theme $theme): bool
    {
        $oldTheme = new Theme($theme->getOriginalValues());

        if (file_exists($oldTheme->getPath())) {
            return rename($oldTheme->getPath(), $theme->getPath());
        }

        return $this->createPlugin($theme);
     }
}

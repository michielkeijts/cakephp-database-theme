<?php
declare(strict_types=1);

namespace CakeDatabaseTheme\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use CakeDatabaseTheme\Model\Entity\Template;

/**
 * CakeDatabaseThemesTemplates Model
 *
 * @property \CakeDatabaseTheme\Model\Table\ThemesTable&\Cake\ORM\Association\BelongsTo $Themes
 *
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate newEmptyEntity()
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate newEntity(array $data, array $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate[] newEntities(array $data, array $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate get($primaryKey, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \CakeDatabaseTheme\Model\Entity\CakeDatabaseThemesTemplate[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TemplatesTable extends Table
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

        $this->setTable('cake_database_themes_templates');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->setEntityClass(Template::class);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Themes', [
            'foreignKey' => 'theme_id',
            'joinType' => 'INNER',
            'className' => 'CakeDatabaseTheme.Themes',
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
            ->scalar('name')
            ->maxLength('name', 45)
            ->allowEmptyString('name');

        $validator
            ->scalar('value')
            ->maxLength('value', 4294967295)
            ->allowEmptyString('value');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

        $validator
            ->integer('deleted_by')
            ->allowEmptyString('deleted_by');

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
        $rules->add($rules->existsIn(['theme_id'], 'Themes'));

        return $rules;
    }
    
    /**
     * Prevent updating the name field as this changes the path
     * @param EventInterface $event
     * @param Template $template
     */
    public function beforeSave(EventInterface $event, Template $template) 
    {
        if ($template->isDirty('name') && !$template->isNew()) {
            $template->set('name', $template->getOriginal('name'));
            $template->setDirty('name', false);
        }
    }
    
    /**
     * Make sure the template exists as file
     * @param EventInterface $event
     * @param Template $entity
     */
    public function afterSave(EventInterface $event, Template $template) 
    {
        if ($entity->isDirty('name') || $entity->isDirty('content') && Configure::read('CakeDatabaseTheme.lazyLoad')) {
            $this->updateTemplateFileInPlugin();
        }
    }
        
    /**
     * Update a file in the plugin directory  
     * @param Template $template
     * @param bool $force
     * @return bool
     */
    public function updateTemplateFileInPlugin(Template $template): bool
    {
        return TRUE;
    }
}

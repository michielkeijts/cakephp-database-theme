<?php
declare(strict_types=1);

namespace CakeDatabaseTheme\Test\TestCase\Model\Table;

use CakeDatabaseTheme\Model\Table\CakeDatabaseThemesTemplatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * CakeDatabaseTheme\Model\Table\CakeDatabaseThemesTemplatesTable Test Case
 */
class CakeDatabaseThemesTemplatesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \CakeDatabaseTheme\Model\Table\CakeDatabaseThemesTemplatesTable
     */
    protected $CakeDatabaseThemesTemplates;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.CakeDatabaseTheme.CakeDatabaseThemesTemplates',
        'plugin.CakeDatabaseTheme.Themes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CakeDatabaseThemesTemplates') ? [] : ['className' => CakeDatabaseThemesTemplatesTable::class];
        $this->CakeDatabaseThemesTemplates = TableRegistry::getTableLocator()->get('CakeDatabaseThemesTemplates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->CakeDatabaseThemesTemplates);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

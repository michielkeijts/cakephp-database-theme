<?php
declare(strict_types=1);

namespace CakeDatabaseTheme\Test\TestCase\Model\Table;

use CakeDatabaseTheme\Model\Table\CakeDatabaseThemesThemesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * CakeDatabaseTheme\Model\Table\CakeDatabaseThemesThemesTable Test Case
 */
class CakeDatabaseThemesThemesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \CakeDatabaseTheme\Model\Table\CakeDatabaseThemesThemesTable
     */
    protected $CakeDatabaseThemesThemes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.CakeDatabaseTheme.CakeDatabaseThemesThemes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('CakeDatabaseThemesThemes') ? [] : ['className' => CakeDatabaseThemesThemesTable::class];
        $this->CakeDatabaseThemesThemes = TableRegistry::getTableLocator()->get('CakeDatabaseThemesThemes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->CakeDatabaseThemesThemes);

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

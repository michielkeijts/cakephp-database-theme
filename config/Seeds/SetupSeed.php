<?php
/*
 * @copyright (C) 2020 Michiel Keijts, Normit
 * 
 */

use Migrations\AbstractSeed;

/**
 * Description of Setup
 *
 * @author michiel
 */
class SetupSeed extends AbstractSeed {
    
    /**
     * This restores a Default theme and places it at the top of the Hierarchy (MySQL related)
     */
    public function run()
    {
        $tableName = 'cake_database_themes_themes';
    
        $table = $this->table($tableName);
        
        $result = $this->fetchAll(sprintf("SELECT id FROM {$tableName} WHERE name = 'Default'"));
        
        if (empty($result)) {
            $this->execute("UPDATE {$tableName} SET `lft` = `lft` + 1, `rght` = `rght` + 1");
            $this->execute("INSERT INTO {$tableName} (`name`, `lft`, `rght`) SELECT 'Default' AS `name`, 1 AS `lft`, (IFNULL(MAX(`rght`),1) + 1) AS `rght` FROM {$tableName}");
            
            foreach ($this->fetchAll("SELECT id FROM {$tableName} WHERE `name` = 'Default' LIMIT 1") as $row) {
                $this->execute("UPDATE {$tableName} SET parent_id = {$row['id']} WHERE parent_id IS NULL AND id != {$row['id']}");
            }
            
        }
    }
}

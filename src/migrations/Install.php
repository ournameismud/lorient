<?php
/**
 * Lorient plugin for Craft CMS 3.x
 *
 * Custom functionality for Lorient CMS
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\lorient\migrations;

use ournameismud\lorient\Lorient;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

   /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%lorient_addresses}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%lorient_addresses}}',
                [
                    'id' => $this->primaryKey(),
                    'title' => $this->string(255)->defaultValue(''),
                    'firstName' => $this->string(255)->notNull()->defaultValue(''),
                    'secondName' => $this->string(255)->notNull()->defaultValue(''),
                    'company' => $this->string(255)->notNull()->defaultValue(''),
                    'address1' => $this->string(255)->notNull()->defaultValue(''),
                    'address2' => $this->string(255)->defaultValue(''),
                    'townCity' => $this->string(255)->notNull()->defaultValue(''),
                    'state' => $this->string(255)->notNull()->defaultValue(''),
                    'postcode' => $this->string(255)->notNull()->defaultValue(''),
                    'telephone' => $this->string(255)->notNull()->defaultValue(''),
                    'email' => $this->string(255)->notNull()->defaultValue(''),
                    'owner' => $this->string(255)->notNull()->defaultValue(''),
                    'siteId' => $this->integer()->notNull(), 
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%lorient_favourites}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%lorient_favourites}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    'owner' => $this->string(255)->notNull(),
                    'element' => $this->integer()->notNull(),
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%lorient_orders}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%lorient_orders}}',
                [
                    'id' => $this->primaryKey(),
                    'owner' => $this->string(255)->notNull()->defaultValue(''),
                    'projectRef' => $this->string(255)->defaultValue(''),
                    'status' => $this->string(255)->notNull()->defaultValue('draft'),
                    'addressId' => $this->integer()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'siteId' => $this->integer()->notNull(),
                    'uid' => $this->uid()
                ]
            );
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%lorient_samples}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%lorient_samples}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    'owner' => $this->string(255)->notNull()->defaultValue(''),
                    'specs' => $this->text(),
                    'order' => $this->integer(),
                    'element' => $this->integer()->notNull(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                '{{%lorient_addresses}}',
                'some_field',
                true
            ),
            '{{%lorient_addresses}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

        $this->createIndex(
            $this->db->getIndexName(
                '{{%lorient_favourites}}',
                'some_field',
                true
            ),
            '{{%lorient_favourites}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

        $this->createIndex(
            $this->db->getIndexName(
                '{{%lorient_orders}}',
                'some_field',
                true
            ),
            '{{%lorient_orders}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

        $this->createIndex(
            $this->db->getIndexName(
                '{{%lorient_samples}}',
                'some_field',
                true
            ),
            '{{%lorient_samples}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%lorient_addresses}}', 'siteId'),
            '{{%lorient_addresses}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%lorient_favourites}}', 'siteId'),
            '{{%lorient_favourites}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%lorient_orders}}', 'siteId'),
            '{{%lorient_orders}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%lorient_samples}}', 'siteId'),
            '{{%lorient_samples}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%lorient_addresses}}');

        $this->dropTableIfExists('{{%lorient_favourites}}');

        $this->dropTableIfExists('{{%lorient_orders}}');

        $this->dropTableIfExists('{{%lorient_samples}}');
    }
}

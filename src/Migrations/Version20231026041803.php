<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231026041803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $adminActivity = $schema->createTable('admin_activities');
        $adminActivity->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $adminActivity->addColumn('admin_id', Types::INTEGER, ['notnull' => true]);
        $adminActivity->addColumn('action', Types::STRING, ['notnull' => false, 'length' => 255]);
        $adminActivity->addColumn('timestamp', Types::DATETIME_MUTABLE, ['notnull' => true]);
        $adminActivity->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('admin_activities');

    }
}

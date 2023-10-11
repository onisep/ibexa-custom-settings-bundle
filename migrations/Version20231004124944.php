<?php

namespace Onisep\migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231004124944 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ibexa_custom_settings (id INT AUTO_INCREMENT NOT NULL, location_id INT NOT NULL, setting_key VARCHAR(300) NOT NULL, setting_value LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;');
    }
}

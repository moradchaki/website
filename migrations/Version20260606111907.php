<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260606111907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart RENAME INDEX uniq_cart_session TO UNIQ_BA388B7613FECDF');
        $this->addSql('ALTER TABLE `order` CHANGE status status VARCHAR(255) DEFAULT \'\'\'pending\'\'\' NOT NULL');
        $this->addSql('ALTER TABLE product_comparison RENAME INDEX uniq_comparison_session TO UNIQ_762D99FD613FECDF');
        $this->addSql('DROP INDEX unique_user_product_like ON product_like');
        $this->addSql('ALTER TABLE product_like CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart RENAME INDEX uniq_ba388b7613fecdf TO UNIQ_CART_SESSION');
        $this->addSql('ALTER TABLE `order` CHANGE status status VARCHAR(255) DEFAULT \'pending\' NOT NULL');
        $this->addSql('ALTER TABLE product_comparison RENAME INDEX uniq_762d99fd613fecdf TO UNIQ_COMPARISON_SESSION');
        $this->addSql('ALTER TABLE product_like CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX unique_user_product_like ON product_like (user_id, product_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260605200124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_like (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_id INT NOT NULL, product_id INT NOT NULL, UNIQUE INDEX unique_user_product_like (user_id, product_id), INDEX IDX_218B6212A76ED395 (user_id), INDEX IDX_218B62124584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE product_like ADD CONSTRAINT FK_218B6212A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_like ADD CONSTRAINT FK_218B62124584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD likes_count INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_like DROP FOREIGN KEY FK_218B6212A76ED395');
        $this->addSql('ALTER TABLE product_like DROP FOREIGN KEY FK_218B62124584665A');
        $this->addSql('DROP TABLE product_like');
        $this->addSql('ALTER TABLE product DROP likes_count');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260605120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix database schema: DECIMAL for money, ON DELETE CASCADE, unique session_id, defaults';
    }

    public function up(Schema $schema): void
    {
        // Drop existing foreign keys to rebuild them with ON DELETE
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25271AD5CDBF');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25274584665A');
        $this->addSql('ALTER TABLE flash_deal DROP FOREIGN KEY FK_D8BBCF454584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F094584665A');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C64584665A');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');

        // Change DOUBLE PRECISION to DECIMAL for monetary columns
        $this->addSql('ALTER TABLE product MODIFY price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE product MODIFY sale_price NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY total_amount NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY subtotal NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY tax_amount NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY shipping_amount NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item MODIFY unit_price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE order_item MODIFY total_price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE cart_item MODIFY unit_price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE flash_deal MODIFY discount_percent NUMERIC(5, 2) NOT NULL');

        // Add default value for order.status
        $this->addSql("ALTER TABLE `order` ALTER status SET DEFAULT 'pending'");

        // Add unique constraints on session_id columns
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CART_SESSION ON cart (session_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_COMPARISON_SESSION ON product_comparison (session_id)');

        // Re-add foreign keys with ON DELETE CASCADE / SET NULL
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25274584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE flash_deal ADD CONSTRAINT FK_D8BBCF454584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C64584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign keys
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25271AD5CDBF');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25274584665A');
        $this->addSql('ALTER TABLE flash_deal DROP FOREIGN KEY FK_D8BBCF454584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F094584665A');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C64584665A');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');

        // Drop unique indexes
        $this->addSql('DROP INDEX UNIQ_CART_SESSION ON cart');
        $this->addSql('DROP INDEX UNIQ_COMPARISON_SESSION ON product_comparison');

        // Remove default from order.status
        $this->addSql("ALTER TABLE `order` ALTER status DROP DEFAULT");

        // Revert DECIMAL back to DOUBLE PRECISION
        $this->addSql('ALTER TABLE product MODIFY price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE product MODIFY sale_price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY total_amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY subtotal DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY tax_amount DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` MODIFY shipping_amount DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item MODIFY unit_price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE order_item MODIFY total_price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE cart_item MODIFY unit_price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE flash_deal MODIFY discount_percent DOUBLE PRECISION NOT NULL');

        // Re-add foreign keys without ON DELETE
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25274584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE flash_deal ADD CONSTRAINT FK_D8BBCF454584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}

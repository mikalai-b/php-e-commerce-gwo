<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240609204001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE orders_promotions (order_id INT NOT NULL, promotion_id INT NOT NULL, INDEX IDX_8FD929B38D9F6D38 (order_id), UNIQUE INDEX UNIQ_8FD929B3139DF194 (promotion_id), PRIMARY KEY(order_id, promotion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orders_promotions ADD CONSTRAINT FK_8FD929B38D9F6D38 FOREIGN KEY (order_id) REFERENCES bookshop_order (id)');
        $this->addSql('ALTER TABLE orders_promotions ADD CONSTRAINT FK_8FD929B3139DF194 FOREIGN KEY (promotion_id) REFERENCES bookshop_promotion (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders_promotions DROP FOREIGN KEY FK_8FD929B38D9F6D38');
        $this->addSql('ALTER TABLE orders_promotions DROP FOREIGN KEY FK_8FD929B3139DF194');
        $this->addSql('DROP TABLE orders_promotions');
    }
}

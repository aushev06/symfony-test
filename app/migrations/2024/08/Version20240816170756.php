<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240816170756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contract_applications (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, representative_name VARCHAR(255) NOT NULL, representative_code VARCHAR(255) NOT NULL, contract_type VARCHAR(255) NOT NULL, applicant_full_name VARCHAR(255) NOT NULL, contact_name VARCHAR(255) NOT NULL, applicant_name VARCHAR(255) NOT NULL, contact_phone VARCHAR(255) NOT NULL, bank_name VARCHAR(255) NOT NULL, bank_account VARCHAR(255) NOT NULL, seats INT UNSIGNED NOT NULL, signboard VARCHAR(255) NOT NULL, shop_type VARCHAR(255) NOT NULL, cuisine_type VARCHAR(255) NOT NULL, client_format VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_125E73242ADD6D8C (supplier_id), INDEX IDX_125E7324B03A8386 (created_by_id), INDEX IDX_125E7324896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE limit_applications (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, `limit` INT UNSIGNED NOT NULL, plan INT UNSIGNED NOT NULL, potential INT UNSIGNED NOT NULL, delay INT UNSIGNED NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_DB184E132ADD6D8C (supplier_id), INDEX IDX_DB184E13B03A8386 (created_by_id), INDEX IDX_DB184E13896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(512) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5C93B3A477153098 (code), INDEX IDX_5C93B3A4B03A8386 (created_by_id), INDEX IDX_5C93B3A4896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suppliers (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, name VARCHAR(512) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_AC28B95C5E237E06 (name), INDEX IDX_AC28B95CB03A8386 (created_by_id), INDEX IDX_AC28B95C896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, login VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9AA08CB10 (login), INDEX IDX_1483A5E9166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contract_applications ADD CONSTRAINT FK_125E73242ADD6D8C FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contract_applications ADD CONSTRAINT FK_125E7324B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE contract_applications ADD CONSTRAINT FK_125E7324896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE limit_applications ADD CONSTRAINT FK_DB184E132ADD6D8C FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE limit_applications ADD CONSTRAINT FK_DB184E13B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE limit_applications ADD CONSTRAINT FK_DB184E13896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE suppliers ADD CONSTRAINT FK_AC28B95CB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE suppliers ADD CONSTRAINT FK_AC28B95C896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract_applications DROP FOREIGN KEY FK_125E73242ADD6D8C');
        $this->addSql('ALTER TABLE contract_applications DROP FOREIGN KEY FK_125E7324B03A8386');
        $this->addSql('ALTER TABLE contract_applications DROP FOREIGN KEY FK_125E7324896DBBDE');
        $this->addSql('ALTER TABLE limit_applications DROP FOREIGN KEY FK_DB184E132ADD6D8C');
        $this->addSql('ALTER TABLE limit_applications DROP FOREIGN KEY FK_DB184E13B03A8386');
        $this->addSql('ALTER TABLE limit_applications DROP FOREIGN KEY FK_DB184E13896DBBDE');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4B03A8386');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4896DBBDE');
        $this->addSql('ALTER TABLE suppliers DROP FOREIGN KEY FK_AC28B95CB03A8386');
        $this->addSql('ALTER TABLE suppliers DROP FOREIGN KEY FK_AC28B95C896DBBDE');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9166D1F9C');
        $this->addSql('DROP TABLE contract_applications');
        $this->addSql('DROP TABLE limit_applications');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE suppliers');
        $this->addSql('DROP TABLE users');
    }
}

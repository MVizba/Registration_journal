<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212135555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, patient_id INT NOT NULL, date DATE NOT NULL, registration_date DATE NOT NULL, symptoms_date DATE NOT NULL, status LONGTEXT NOT NULL, diagnosis LONGTEXT NOT NULL, services LONGTEXT DEFAULT NULL, end_result LONGTEXT DEFAULT NULL, INDEX IDX_FE38F84419EB6921 (client_id), INDEX IDX_FE38F8446B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE asigned_drugs (id INT AUTO_INCREMENT NOT NULL, drug_warehouse_id INT NOT NULL, appointment_id INT DEFAULT NULL, date DATE NOT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_5E2491348A09E3D (drug_warehouse_id), INDEX IDX_5E24913E5B533F9 (appointment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, address VARCHAR(100) DEFAULT NULL, phone VARCHAR(30) NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_C7440455A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drug_warehouse (id INT AUTO_INCREMENT NOT NULL, date_of_receipt DATE NOT NULL, drug_name VARCHAR(255) NOT NULL, drug_manufacturer VARCHAR(255) NOT NULL, document_number VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, type VARCHAR(100) NOT NULL, manufacture_date DATE DEFAULT NULL, expiration_date DATE NOT NULL, series VARCHAR(255) NOT NULL, where_obtained_from VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE examination (id INT AUTO_INCREMENT NOT NULL, shortcut VARCHAR(20) NOT NULL, examination_name VARCHAR(200) NOT NULL, norms VARCHAR(200) DEFAULT NULL, machine VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE examination_with_results (id INT AUTO_INCREMENT NOT NULL, examination_id INT NOT NULL, appointment_id INT DEFAULT NULL, date DATE NOT NULL, result LONGTEXT NOT NULL, INDEX IDX_4ACAF1F7DAD0CFBF (examination_id), INDEX IDX_4ACAF1F7E5B533F9 (appointment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, name VARCHAR(50) NOT NULL, type VARCHAR(50) NOT NULL, gender VARCHAR(50) NOT NULL, age DATE DEFAULT NULL, marking_number VARCHAR(50) DEFAULT NULL, passport_number VARCHAR(50) DEFAULT NULL, appearance LONGTEXT NOT NULL, INDEX IDX_1ADAD7EB19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(30) NOT NULL, last_name VARCHAR(30) NOT NULL, position VARCHAR(30) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8446B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE asigned_drugs ADD CONSTRAINT FK_5E2491348A09E3D FOREIGN KEY (drug_warehouse_id) REFERENCES drug_warehouse (id)');
        $this->addSql('ALTER TABLE asigned_drugs ADD CONSTRAINT FK_5E24913E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE examination_with_results ADD CONSTRAINT FK_4ACAF1F7DAD0CFBF FOREIGN KEY (examination_id) REFERENCES examination (id)');
        $this->addSql('ALTER TABLE examination_with_results ADD CONSTRAINT FK_4ACAF1F7E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F84419EB6921');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8446B899279');
        $this->addSql('ALTER TABLE asigned_drugs DROP FOREIGN KEY FK_5E2491348A09E3D');
        $this->addSql('ALTER TABLE asigned_drugs DROP FOREIGN KEY FK_5E24913E5B533F9');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('ALTER TABLE examination_with_results DROP FOREIGN KEY FK_4ACAF1F7DAD0CFBF');
        $this->addSql('ALTER TABLE examination_with_results DROP FOREIGN KEY FK_4ACAF1F7E5B533F9');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EB19EB6921');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE asigned_drugs');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE drug_warehouse');
        $this->addSql('DROP TABLE examination');
        $this->addSql('DROP TABLE examination_with_results');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE user');
    }
}

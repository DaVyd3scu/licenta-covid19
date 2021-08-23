<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210823101534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE active_cases (date DATE NOT NULL, county_code VARCHAR(255) DEFAULT NULL, current_day_number INT NOT NULL, INDEX IDX_9A26B1CD25103594 (county_code), PRIMARY KEY(date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE astra_zeneca_vaccine (date DATE NOT NULL, current_day_number_of_doses INT NOT NULL, people_immunized INT NOT NULL, PRIMARY KEY(date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE counties (code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deceased_cases (date DATE NOT NULL, current_day_number INT NOT NULL, PRIMARY KEY(date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE healed_cases (date DATE NOT NULL, current_day_number INT NOT NULL, PRIMARY KEY(date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE incidence_rate (date DATE NOT NULL, county_code VARCHAR(255) DEFAULT NULL, incidence_rate INT NOT NULL, INDEX IDX_DE656BC325103594 (county_code), PRIMARY KEY(date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE johnson_johnson_vaccine (date DATE NOT NULL, current_day_number_of_doses INT NOT NULL, people_immunized INT NOT NULL, PRIMARY KEY(date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE last_updated (last_update DATETIME NOT NULL, PRIMARY KEY(last_update)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE moderna_vaccine (date DATE NOT NULL, current_day_number_of_doses INT NOT NULL, people_immunized INT NOT NULL, PRIMARY KEY(date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pfizer_vaccine (date DATE NOT NULL, current_day_number_of_doses INT NOT NULL, people_immunized INT NOT NULL, PRIMARY KEY(date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE total_numbers (date DATE NOT NULL, total_cases INT NOT NULL, doses_of_vaccine_administered INT NOT NULL, PRIMARY KEY(date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE active_cases ADD CONSTRAINT FK_9A26B1CD25103594 FOREIGN KEY (county_code) REFERENCES counties (code)');
        $this->addSql('ALTER TABLE incidence_rate ADD CONSTRAINT FK_DE656BC325103594 FOREIGN KEY (county_code) REFERENCES counties (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE active_cases DROP FOREIGN KEY FK_9A26B1CD25103594');
        $this->addSql('ALTER TABLE incidence_rate DROP FOREIGN KEY FK_DE656BC325103594');
        $this->addSql('DROP TABLE active_cases');
        $this->addSql('DROP TABLE astra_zeneca_vaccine');
        $this->addSql('DROP TABLE counties');
        $this->addSql('DROP TABLE deceased_cases');
        $this->addSql('DROP TABLE healed_cases');
        $this->addSql('DROP TABLE incidence_rate');
        $this->addSql('DROP TABLE johnson_johnson_vaccine');
        $this->addSql('DROP TABLE last_updated');
        $this->addSql('DROP TABLE moderna_vaccine');
        $this->addSql('DROP TABLE pfizer_vaccine');
        $this->addSql('DROP TABLE total_numbers');
    }
}

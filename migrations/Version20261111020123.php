<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261111020123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema: note, tag and note_tag tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE note (
              id INT AUTO_INCREMENT NOT NULL,
              title LONGTEXT NOT NULL,
              description LONGTEXT DEFAULT NULL,
              created_at DATETIME NOT NULL,
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE note_tag (
              note_id INT NOT NULL,
              tag_id INT NOT NULL,
              INDEX IDX_737A976326ED0855 (note_id),
              INDEX IDX_737A9763BAD26311 (tag_id),
              PRIMARY KEY(note_id, tag_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tag (
              id INT AUTO_INCREMENT NOT NULL,
              name LONGTEXT NOT NULL,
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              note_tag
            ADD
              CONSTRAINT FK_737A976326ED0855 FOREIGN KEY (note_id) REFERENCES note (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              note_tag
            ADD
              CONSTRAINT FK_737A9763BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note_tag DROP FOREIGN KEY FK_737A976326ED0855');
        $this->addSql('ALTER TABLE note_tag DROP FOREIGN KEY FK_737A9763BAD26311');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE note_tag');
        $this->addSql('DROP TABLE tag');
    }
}

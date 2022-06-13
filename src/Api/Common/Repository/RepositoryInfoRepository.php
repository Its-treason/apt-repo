<?php

namespace ItsTreason\AptRepo\Api\Common\Repository;

use PDO;

class RepositoryInfoRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    public function getValue(string $field): string|null
    {
        $sql = <<<SQL
            SELECT value FROM `repository_info` WHERE field = :field
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'field' => $field,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $row['value'];
    }

    public function setValue(string $field, string $value): void
    {
        $sql = <<<SQL
            UPDATE `repository_info`
            SET `value` = :value
            WHERE `field` = :field; 
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'field' => $field,
            'value' => $value,
        ]);
    }
}

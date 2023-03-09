<?php

namespace ItsTreason\AptRepo\Repository;

use ItsTreason\AptRepo\Value\Suite;
use PDO;

class SuitesRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    /**
     * @return Suite[]
     */
    public function getAll(): array
    {
        $sql = <<<SQL
            SELECT * FROM suites
        SQL;

        $query = $this->pdo->query($sql);

        /** @var Suite[] $suites */
        $suites = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $suites[] = Suite::fromValues($row['codename'], $row['suite']);
        }

        return $suites;
    }

    public function create(Suite $suite): void
    {
        $sql = <<<SQL
            INSERT INTO suites (codename, suite) VALUES (:codename, :suite)
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'codename' => $suite->getCodename(),
            'suite' => $suite->getSuite(),
        ]);
    }

    public function exists(Suite $suite): bool
    {
        $sql = <<<SQL
            SELECT * FROM suites WHERE codename = :codename AND suite = :suite
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'codename' => $suite->getCodename(),
            'suite' => $suite->getSuite(),
        ]);

        return $statement->fetch() !== false;
    }
}

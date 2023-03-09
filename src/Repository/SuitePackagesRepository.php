<?php

namespace ItsTreason\AptRepo\Repository;

use ItsTreason\AptRepo\Value\PackageMetadata;
use ItsTreason\AptRepo\Value\Suite;
use PDO;

class SuitePackagesRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {}

    public function getAllPackagesForSuite(Suite $suite): array
    {
        $sql = <<<SQL
            SELECT * FROM suite_packages 
            INNER JOIN package_metadata USING package_id 
            WHERE
                suite_packages.codename = :codename AND suite_packages.suite = :suite
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'codename' => $suite->getCodename(),
            'suite' => $suite->getSuite(),
        ]);

        $packages = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $packages[] = PackageMetadata::fromDbRow($row);
        }

        return $packages;
    }
}

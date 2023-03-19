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

    /**
     * @param PackageMetadata $packageMetadata
     * @return Suite[]
     */
    public function getAllSuitesForPackage(PackageMetadata $packageMetadata): array
    {
        $sql = <<<SQL
            SELECT * FROM suite_packages 
            WHERE
                package_id = :package_id
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'package_id' => $packageMetadata->getId(),
        ]);

        $suites = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $suites[] = Suite::fromValues($row['codename'], $row['suite']);
        }

        return $suites;
    }

    public function insertPackageIntoSuite(PackageMetadata $package, Suite $suite): void
    {
        $sql = <<<SQL
            INSERT INTO `suite_packages`
                (`codename`, `suite`, `package_id`) 
            VALUES (:codename, :suite, :packageId) 
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'codename' => $suite->getCodename(),
            'suite' => $suite->getSuite(),
            'packageId' => $package->getId(),
        ]);
    }

    public function removePackageFromSuite(PackageMetadata $package, Suite $suite): void
    {
        $sql = <<<SQL
            DELETE FROM `suite_packages`
            WHERE codename = :codename AND
                  suite = :suite AND
                  package_id = :packageId
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'codename' => $suite->getCodename(),
            'suite' => $suite->getSuite(),
            'packageId' => $package->getId(),
        ]);
    }

    public function removePackageFromAllSuites(PackageMetadata $package): void
    {
        $sql = <<<SQL
            DELETE FROM `suite_packages`
            WHERE package_id = :packageId
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'packageId' => $package->getId(),
        ]);
    }
}

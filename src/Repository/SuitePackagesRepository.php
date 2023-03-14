<?php

namespace ItsTreason\AptRepo\Repository;

use ItsTreason\AptRepo\Value\PackageMetadata;
use ItsTreason\AptRepo\Value\Suite;
use PDO;

class SuitePackagesRepository
{
    public function __construct(
        private readonly PDO $pdo
    )
    {
    }

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

    /**
     * @param PackageMetadata $packageMetadata
     * @return Suite[]
     */
    public function getAllPackagesForPackage(PackageMetadata $packageMetadata): array
    {
        $sql = <<<SQL
            SELECT * FROM suite_packages 
            INNER JOIN package_metadata USING(package_id)
            WHERE
                package_metadata.package_id = :package_id
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
}

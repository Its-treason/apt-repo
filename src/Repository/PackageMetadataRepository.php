<?php

namespace ItsTreason\AptRepo\Repository;

use ItsTreason\AptRepo\Value\PackageMetadata;
use ItsTreason\AptRepo\Value\Suite;
use PDO;

class PackageMetadataRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    public function insertPackageMetadata(PackageMetadata $metadata): void
    {
        $sql = <<<SQL
            INSERT INTO `package_metadata`
                (`package_id`, `name`, `version`, `arch`, `filename`, `fullinfo`, `upload_date`)
            VALUES
                (:id, :name, :version, :arch, :filename, :fullInfo, :uploadDate) 
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id' => $metadata->getId(),
            'name' => $metadata->getName(),
            'version' => $metadata->getVersion(),
            'arch' => $metadata->getArch(),
            'filename' => $metadata->getFilename(),
            'fullInfo' => $metadata->getFullInfo(),
            'uploadDate' => $metadata->getUploadDate()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return string[]
     */
    public function getAllPackageNames(string $search = ''): array
    {
        $sql = <<<SQL
            SELECT `name`
            FROM `package_metadata`
            WHERE name LIKE :search
            GROUP BY name
            ORDER BY name
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'search' => '%' . $search . '%',
        ]);

        $packages = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $packages[] = $row['name'];
        }

        return $packages;
    }

    /**
     * @return PackageMetadata[]
     */
    public function getAllPackages(string $search = ''): array
    {
        $sql = <<<SQL
            SELECT *
            FROM `package_metadata`
            WHERE name LIKE :search
            ORDER BY name, version DESC, arch
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'search' => '%' . $search . '%',
        ]);

        $packages = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $packages[] = PackageMetadata::fromDbRow($row);
        }

        return $packages;
    }

    /**
     * @return PackageMetadata[]
     */
    public function getAllPackagesForSuite(Suite $suite): array
    {
        $sql = <<<SQL
            SELECT *
            FROM `package_metadata`
            INNER JOIN suite_packages USING (package_id)
            WHERE codename = :codename AND suite = :suite
            GROUP BY name
            ORDER BY name
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

    public function getPackageByFilename(string $filename): PackageMetadata|null
    {
        $sql = <<<SQL
            SELECT * FROM `package_metadata`
            WHERE 
                filename = :fileName
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
           'fileName' => $filename,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return PackageMetadata::fromDbRow($row);
    }

    public function deletePackage(PackageMetadata $package): void
    {
        $sql = <<<SQL
            DELETE FROM package_metadata WHERE package_id = :packageId
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'packageId' => $package->getId(),
        ]);
    }

    /**
     * @return string[]
     */
    public function getAllArches(Suite $suite): array
    {
        $sql = <<<SQL
            SELECT `package_metadata`.arch FROM `package_metadata`
            INNER JOIN suite_packages USING (package_id)
            WHERE codename = :codename AND suite = :suite
            GROUP BY `package_metadata`.arch
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'codename' => $suite->getCodename(),
            'suite' => $suite->getSuite(),
        ]);

        $arches = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $arches[] = $row['arch'];
        }

        return $arches;
    }

    /**
     * @return string[]
     */
    public function getAllArchesForCodename(string $codename): array
    {
        $sql = <<<SQL
            SELECT `package_metadata`.arch FROM `package_metadata`
            INNER JOIN suite_packages USING (package_id)
            WHERE codename = :codename
            GROUP BY `package_metadata`.arch
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'codename' => $codename,
        ]);

        $arches = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $arches[] = $row['arch'];
        }

        return $arches;
    }
}

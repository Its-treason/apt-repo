<?php

namespace ItsTreason\AptRepo\Api\Common\Repository;

use ItsTreason\AptRepo\Value\PackageMetadata;
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
            'id' => $metadata->getId()->asString(),
            'name' => $metadata->getName(),
            'version' => $metadata->getVersion(),
            'arch' => $metadata->getArch(),
            'filename' => $metadata->getFilename(),
            'fullInfo' => $metadata->getFullInfo(),
            'uploadDate' => $metadata->getUploadDate()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return PackageMetadata[]
     */
    public function getAllPackages(): array
    {
        $sql = <<<SQL
            SELECT *, MAX(`upload_date`)
            FROM `package_metadata`
            GROUP BY name
            ORDER BY name 
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        $packages = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $packages[] = PackageMetadata::fromDbRow($row);
        }

        return $packages;
    }

    public function getPackageByFilename(string $filename): PackageMetadata|null
    {
        $sql = <<<SQL
            SELECT *, MAX(`upload_date`) FROM `package_metadata`
            WHERE 
                filename = :fileName
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
           'fileName' => sprintf('pool/main/%s', $filename),
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return PackageMetadata::fromDbRow($row);
    }

    /**
     * @return string[]
     */
    public function getAllArches(): array
    {
        $sql = <<<SQL
            SELECT arch FROM `package_metadata` GROUP BY arch
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        $arches = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $arches[] = $row['arch'];
        }

        return $arches;
    }
}

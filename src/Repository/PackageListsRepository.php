<?php

namespace ItsTreason\AptRepo\Repository;

use ItsTreason\AptRepo\Value\PackageList;
use ItsTreason\AptRepo\Value\Suite;
use PDO;

class PackageListsRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    public function updatePackageList(PackageList $packageList): void
    {
        $sql = <<<SQL
            INSERT INTO `package_lists`
                (`arch`, `type`, `codename`, `suite`, `content`, `size`, `md5sum`, `sha1`, `sha256`)
            VALUES 
                (:arch, :type: :codename, :suite, :path, :content, :size, :md5sum, :sha1, :sha256)
            ON DUPLICATE KEY UPDATE
                content = :content,
                size = :size,
                md5sum = :md5sum,
                sha1 = :sha1,
                sha256 = :sha256
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'arch' => $packageList->getArch(),
            'type' => $packageList->getType(),
            'codename' => $packageList->getCodename(),
            'suite' => $packageList->getSuite(),
            'content' => $packageList->getContent(),
            'size' => $packageList->getSize(),
            'md5sum' => $packageList->getMd5sum(),
            'sha1' => $packageList->getSha1(),
            'sha256' => $packageList->getSha256(),
        ]);
    }

    public function getPackageList(string $arch, string $type, Suite $suite): PackageList|null
    {
        $sql = <<<SQL
            SELECT * FROM `package_lists` 
            WHERE
                arch = :arch AND
                type = :type AND
                codename = :codename AND
                suite = :suite
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'arch' => $arch,
            'type' => $type,
            'codename' => $suite->getCodename(),
            'suite' => $suite->getSuite(),
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return PackageList::fromDbRow($row);
    }

    /**
     * @return PackageList[]
     */
    public function getAllPackageListsForSuites(Suite $suite): array
    {
        $sql = <<<SQL
            SELECT * FROM `package_lists` WHERE codename = :codename AND suite = :suite
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'codename' => $suite->getCodename(),
            'suite' => $suite->getSuite(),
        ]);

        $packageLists = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $packageLists[] = PackageList::fromDbRow($row);
        }

        return $packageLists;
    }
}

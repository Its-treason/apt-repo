<?php

namespace ItsTreason\AptRepo\Api\Common\Repository;

use ItsTreason\AptRepo\Value\PackageList;
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
                (`path`, `content`, `size`, `md5sum`, `sha1`, `sha256`)
            VALUES 
                (:path, :content, :size, :md5sum, :sha1, :sha256)
            ON DUPLICATE KEY UPDATE
                content = :content,
                size = :size,
                md5sum = :md5sum,
                sha1 = :sha1,
                sha256 = :sha256
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'path' => $packageList->getPath(),
            'content' => $packageList->getContent(),
            'size' => $packageList->getSize(),
            'md5sum' => $packageList->getMd5sum(),
            'sha1' => $packageList->getSha1(),
            'sha256' => $packageList->getSha256(),
        ]);
    }

    public function getPackageList(string $path): PackageList|null
    {
        $sql = <<<SQL
            SELECT * FROM `package_lists` WHERE path = :path
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'path' => $path,
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
    public function getAllPackageLists(): array
    {
        $sql = <<<SQL
            SELECT * FROM `package_lists`
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        $packageLists = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $packageLists[] = PackageList::fromDbRow($row);
        }

        return $packageLists;
    }
}

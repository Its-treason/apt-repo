<?php

namespace ItsTreason\AptRepo\Repository;

use ItsTreason\AptRepo\Value\GitHubSubscription;
use PDO;

class GitHubSubscriptionRepository
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
            SELECT * FROM github_subscription
        SQL;

        $query = $this->pdo->query($sql);

        /** @var GitHubSubscription[] $subscriptions */
        $subscriptions = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $subscriptions[] = GitHubSubscription::fromDbRow($row);
        }

        return $subscriptions;
    }

    /**
     * @return GitHubSubscription[]
     */
    public function getByOwnerName(string $owner, string $name): ?GitHubSubscription
    {
        $sql = <<<SQL
            SELECT * FROM github_subscription WHERE owner = :owner AND name = :name
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['owner' => $owner, 'name' => $name ]);

        $row = $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return GitHubSubscription::fromDbRow($row);
    }

    public function insert(GitHubSubscription $subscription): void
    {
        $sql = <<<SQL
        INSERT INTO github_subscription
            (owner, name, last_release)
        VALUES
            (:owner, :name, :last_release)
        ON DUPLICATE KEY UPDATE
            last_release  = :last_release
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'owner' => $subscription->getOwner(),
            'name' => $subscription->getName(),
            'last_release' => $subscription->getLastRelease(),
        ]);
    }

    public function delete(GitHubSubscription $subscription): bool
    {
        $sql = <<<SQL
            DELETE FROM github_subscription WHERE owner = :owner AND name = :name
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'owner' => $subscription->getOwner(),
            'name' => $subscription->getName(),
        ]);

        return $statement->rowCount() > 0;
    }
}


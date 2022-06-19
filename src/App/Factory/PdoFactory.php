<?php

namespace ItsTreason\AptRepo\App\Factory;

use Error;
use PDO;
use RuntimeException;

class PdoFactory
{
    public function __invoke(): PDO
    {
        $hostname = getenv('DB_HOST');
        $database = getenv('DB_DATABASE');

        $dsn = 'mysql:host=' . $hostname . ';dbname=' . $database . ';charset=UTF8';
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        try {
            return new PDO($dsn, $username, $password);
        } catch (Error $exception) {
            throw new RuntimeException('Could not connect to DB', 0, $exception);
        }
    }
}

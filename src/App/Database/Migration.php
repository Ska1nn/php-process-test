<?php
declare(strict_types=1);

namespace App\Database;

class Migration
{
    public static function migrate(): void
    {
        $pdo = Connection::getInstance();

        $pdo->exec('CREATE TABLE IF NOT EXISTS processes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL
        )');

        $pdo->exec('CREATE TABLE IF NOT EXISTS fields (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            process_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            type TEXT NOT NULL,
            value TEXT,
            format TEXT,
            FOREIGN KEY (process_id) REFERENCES processes(id)
        )');
    }
}

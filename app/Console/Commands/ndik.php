<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class ndik extends Command
{
    protected $signature = "ndik";
    protected $description = "Clean database and restore from latest backup";

    public function handle()
    {
        if (
            !$this->confirm(
                "⚠️  This will DELETE ALL TABLES and restore latest backup. Continue?",
            )
        ) {
            $this->info("Cancelled.");
            return 0;
        }

        $backupPath = storage_path("app/db/");

        if (!is_dir($backupPath)) {
            $this->error("Backup directory not found.");
            return 1;
        }

        // Cari file SQL
        $sqlFiles = glob($backupPath . "/*.sql");

        if (empty($sqlFiles)) {
            $this->error("No SQL file found after extraction.");
            return 1;
        }

        rsort($sqlFiles);
        $latestSql = $sqlFiles[0];

        $this->info("Cleaning database...");

        DB::statement("SET FOREIGN_KEY_CHECKS=0");

        $tables = DB::select("SHOW TABLES");
        $dbName = config("database.connections.mysql.database");
        $key = "Tables_in_" . $dbName;

        foreach ($tables as $table) {
            $tableName = $table->$key;
            DB::statement("DROP TABLE IF EXISTS `$tableName`");
        }

        DB::statement("SET FOREIGN_KEY_CHECKS=1");

        $this->info("Database cleaned.");

        $this->info("Restoring: " . basename($latestSql));

        $connection = config("database.connections.mysql");

        $host = escapeshellarg($connection["host"]);
        $port = escapeshellarg($connection["port"]);
        $username = escapeshellarg($connection["username"]);
        $password = escapeshellarg($connection["password"]);
        $database = escapeshellarg($connection["database"]);
        $sqlFile = escapeshellarg($latestSql);

        $command = "mysql --host=$host --port=$port --user=$username --password=$password $database < $sqlFile";

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("✅ Database restored successfully!");
            return 0;
        } else {
            $this->error("❌ Database restore failed!");
            return 1;
        }
    }
}

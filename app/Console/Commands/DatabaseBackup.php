<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:database-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the PostgreSQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ensure the backups directory exists
        if (!is_dir(storage_path("app/backups"))) {
            mkdir(storage_path("app/backups"), 0755, true);
        }

        // Get the database connection details from the environment
        $dbHost = env('DB_HOST');
        $dbPort = env('DB_PORT');
        $dbName = env('DB_DATABASE');
        $dbUsername = env('DB_USERNAME');
        $dbPassword = env('DB_PASSWORD');

        // Generate the filename with the current date and time
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filePath = storage_path("app/backups/$filename");

        // Construct the pg_dump command with additional options
        $command = "PGPASSWORD=\"$dbPassword\" pg_dump --host=$dbHost --port=$dbPort --username=$dbUsername --dbname=$dbName --no-owner --quote-all-identifiers --inserts --clean --create > $filePath";

        // Execute the command
        $output = null;
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            // Modify the dump file to handle connection and add IF NOT EXISTS to CREATE DATABASE and CREATE TABLE statements
            $dumpContent = file_get_contents($filePath);

            // Remove the problematic \connect line
            $dumpContent = preg_replace('/\\\\connect "converter".*\n/', '', $dumpContent);

            file_put_contents($filePath, $dumpContent);

            $this->info('Database backup successfully created.');
        } else {
            $this->error('Failed to create database backup.');
        }
    }
}

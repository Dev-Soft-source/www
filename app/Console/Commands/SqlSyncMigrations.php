<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SqlSyncMigrations extends Command
{
    protected $signature = 'sql:sync-migrations 
                            {--dry-run : Only list missing tables, do not create migrations}
                            {--force : Create migrations without confirmation}
                            {--sql= : Path to SQL structure file (default: proximaride.sql in project root)}';

    protected $description = 'Option B: Add migrations for tables/columns in SQL structure that are not in existing migrations.';

    public function handle(): int
    {
        $sqlPath = $this->option('sql') ?: base_path('proximaride.sql');
        if (!File::isFile($sqlPath)) {
            $this->error("SQL file not found: {$sqlPath}");
            return 1;
        }

        $this->info('Reading SQL structure...');
        $sql = File::get($sqlPath);
        $sqlTables = $this->parseCreateTables($sql);
        $this->info('Found ' . count($sqlTables) . ' tables in SQL.');

        $migrationTables = $this->getTablesFromMigrations();
        $this->info('Found ' . count($migrationTables) . ' tables created by migrations.');

        $missing = array_diff(array_keys($sqlTables), $migrationTables);
        $missing = array_values(array_filter($missing, fn ($t) => $t !== 'migrations'));

        if (empty($missing)) {
            $this->info('No missing tables. Schema is in sync (table-level).');
            return 0;
        }

        $this->warn('Tables in SQL but not in migrations: ' . count($missing));
        $this->table(['#', 'Table'], array_map(fn ($t, $i) => [$i + 1, $t], $missing, array_keys($missing)));

        if ($this->option('dry-run')) {
            $this->info('Dry run. Run without --dry-run to create migrations.');
            return 0;
        }

        if (!$this->option('force') && !$this->confirm('Create migrations for these ' . count($missing) . ' tables?', true)) {
            return 0;
        }

        $created = $this->createMigrationsForMissingTables($missing, $sqlTables);
        $this->info("Created {$created} migration(s). Run php artisan migrate to apply.");
        return 0;
    }

    private function parseCreateTables(string $sql): array
    {
        $tables = [];
        $pattern = '/CREATE TABLE\s+`([^`]+)`\s*\(/';
        $offset = 0;
        while (preg_match($pattern, $sql, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $name = $m[1][0];
            $start = $m[0][1];
            $parenStart = strpos($sql, '(', $start);
            $depth = 1;
            $i = $parenStart + 1;
            $len = strlen($sql);
            while ($i < $len && $depth > 0) {
                $c = $sql[$i];
                if ($c === '(') $depth++;
                elseif ($c === ')') $depth--;
                $i++;
            }
            $end = $i;
            while ($end < $len && $sql[$end] !== ';') $end++;
            $fullCreate = trim(substr($sql, $start, $end - $start + 1));
            $tables[$name] = $fullCreate;
            $offset = $end + 1;
        }
        return $tables;
    }

    private function getTablesFromMigrations(): array
    {
        $path = database_path('migrations');
        $tables = [];
        foreach (File::glob($path . '/*.php') as $file) {
            $content = File::get($file);
            if (preg_match_all("/Schema::create\s*\(\s*['\"]([^'\"]+)['\"]/", $content, $m)) {
                foreach ($m[1] as $table) {
                    $tables[$table] = true;
                }
            }
        }
        return array_keys($tables);
    }

    private function createMigrationsForMissingTables(array $missing, array $sqlTables): int
    {
        $migrationsPath = database_path('migrations');
        $date = now()->format('Y_m_d');
        $baseTime = 100000;
        $created = 0;
        foreach ($missing as $index => $table) {
            if (!isset($sqlTables[$table])) {
                continue;
            }
            $createSql = $sqlTables[$table];
            $time = $baseTime + $index;
            $filename = "{$date}_{$time}_create_{$table}_table.php";
            $path = $migrationsPath . DIRECTORY_SEPARATOR . $filename;
            $className = 'Create' . str_replace('_', '', ucwords($table, '_')) . 'Table';
            $className = preg_replace('/[^a-zA-Z0-9]/', '', $className);

            $stub = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
{$this->indentSql($createSql)}
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `{$table}`');
    }
};

PHP;
            File::put($path, $stub);
            $this->line("  Created: {$filename}");
            $created++;
        }
        return $created;
    }

    private function indentSql(string $sql): string
    {
        return implode("\n", array_map(fn ($line) => '        ' . $line, explode("\n", $sql)));
    }
}

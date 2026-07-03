<?php

declare(strict_types=1);

use App\Config\Database;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/config.php';

header('Content-Type: text/html; charset=UTF-8');

$errors = [];
$results = [];

/**
 * Verify environment variables are loaded.
 */

$requiredKeys = [
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD',
    'SUPABASE_URL',
    'SUPABASE_ANON_KEY',
];

foreach ($requiredKeys as $key) {
    if (empty($_ENV[$key] ?? null)) {
        $errors[] = sprintf('Missing environment variable: %s', $key);
    }
}

if (empty($errors)) {
    try {
        $database = new Database();
        $connection = $database->getConnection();

        $results[] = '✓ Environment Loaded';
        $results[] = '✓ Database Connected';
        $results[] = '✓ SSL Connected';

        // Execute current time query.
        $statement = $connection->query('SELECT NOW() AS current_time');
        $timeRow = $statement->fetch();

        if (!empty($timeRow['current_time'])) {
            $results[] = sprintf('✓ Query Executed Successfully (NOW() = %s)', $timeRow['current_time']);
        }

        // List public tables.
        $statement = $connection->query(
            "SELECT table_name FROM information_schema.tables WHERE table_schema='public' ORDER BY table_name"
        );
        $tables = $statement->fetchAll();

        if (!empty($tables)) {
            $results[] = '✓ Tables Found';
        }

        $tableCounts = [];

        foreach ($tables as $table) {
            $tableName = $table['table_name'];
            $countStmt = $connection->query(sprintf('SELECT COUNT(*) AS total FROM %s', $tableName));
            $countRow = $countStmt->fetch();
            $tableCounts[$tableName] = $countRow['total'] ?? 0;
        }

        $results[] = '✓ Database Ready';
    } catch (Throwable $exception) {
        $errors[] = sprintf('Connection error: %s', $exception->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
            line-height: 1.6;
        }
        .status {
            background: #f7f7f7;
            border: 1px solid #ddd;
            padding: 1rem;
            border-radius: 8px;
            max-width: 800px;
        }
        .success {
            color: #1a7f37;
        }
        .error {
            color: #a10303;
        }
        ul {
            margin-top: 0.5rem;
        }
        pre {
            background: #fafafa;
            border: 1px solid #ddd;
            padding: 1rem;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="status">
        <h1>Supabase PostgreSQL Connection Test</h1>

        <?php if (!empty($errors)): ?>
            <h2 class="error">Errors Detected</h2>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
            <h3>Fix Suggestions</h3>
            <ul>
                <li>Ensure `.env` exists and is loaded by `app/config/config.php`.</li>
                <li>Verify Supabase host, port, database, username, and password.</li>
                <li>Confirm PHP PDO PgSQL extension is installed and enabled.</li>
            </ul>
        <?php else: ?>
            <h2 class="success">Connection Summary</h2>
            <ul>
                <?php foreach ($results as $result): ?>
                    <li class="success"><?= htmlspecialchars($result, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>

            <h3>Current Database Time</h3>
            <pre><?= htmlspecialchars($timeRow['current_time'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></pre>

            <h3>Public Tables</h3>
            <ul>
                <?php foreach ($tableCounts as $tableName => $count): ?>
                    <li><strong><?= htmlspecialchars($tableName, ENT_QUOTES, 'UTF-8') ?></strong>: <?= htmlspecialchars((string) $count, ENT_QUOTES, 'UTF-8') ?> rows</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>

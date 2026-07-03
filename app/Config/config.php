<?php

declare(strict_types=1);

namespace App\Config;

use Dotenv\Dotenv;

/**
 * Load environment variables from the project root .env file.
 */
require_once __DIR__ . '/../../vendor/autoload.php';

$projectRoot = dirname(__DIR__, 2);
$dotenv = Dotenv::createImmutable($projectRoot);
$dotenv->safeLoad();

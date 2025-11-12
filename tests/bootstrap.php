<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Dotenv\Dotenv;

$autoload = \dirname(__DIR__).'/vendor/autoload.php';
if (!file_exists($autoload)) {
    // Attempt to install dependencies automatically for reviewers running only ./bin/phpunit
    $cmd = 'composer install --no-interaction --prefer-dist';
    if (\function_exists('passthru')) {
        @passthru($cmd, $exitCode);
    } else {
        @exec($cmd, $out, $exitCode);
    }
    clearstatcache();
    if (!file_exists($autoload)) {
        fwrite(STDERR, '[bootstrap] vendor/autoload.php not found. Please run: composer install'.PHP_EOL);
        exit(1);
    }
}
require $autoload;

if (file_exists(\dirname(__DIR__).'/config/bootstrap.php')) {
    require \dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(\dirname(__DIR__).'/.env');
}

// Ensure test database and schema are ready (runs once at PHPUnit startup)
if (($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) === 'test') {
    try {
        $kernel = new \App\Kernel('test', true);
        $kernel->boot();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        // Create database if not exists
        $application->run(new ArrayInput([
            'command' => 'doctrine:database:create',
            '--if-not-exists' => true,
            '--env' => 'test',
        ]), new NullOutput());

        // Run migrations to latest
        $exitCode = $application->run(new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            '--no-interaction' => true,
            '--env' => 'test',
        ]), new NullOutput());

        if ($exitCode !== 0) {
            fwrite(STDERR, '[bootstrap] Migrations failed with exit code: '.$exitCode.PHP_EOL);
        }
    } catch (\Throwable $e) {
        fwrite(STDERR, '[bootstrap] Test DB setup failed: '.$e->getMessage().PHP_EOL);
    } finally {
        if (isset($kernel)) {
            $kernel->shutdown();
        }
    }
}

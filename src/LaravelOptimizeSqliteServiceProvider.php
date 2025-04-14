<?php

namespace SwissDevjoy\LaravelOptimizeSqlite;

use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelOptimizeSqliteServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-optimize-sqlite')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        Event::listen(static function (ConnectionEstablished $event) {
            if ($event->connection->getDriverName() !== 'sqlite') {
                return;
            }

            $settings = array_filter(array_merge(config('optimize-sqlite.general'), (array) config('optimize-sqlite.databases.'.$event->connection->getName())));

            foreach ($settings as $key => $value) {
                try {
                    $event->connection->unprepared("pragma $key = $value");
                } catch (QueryException $e) {
                    throw_unless(str_contains($e->getMessage(), 'does not exist.'), $e);
                }
            }
        });
    }
}

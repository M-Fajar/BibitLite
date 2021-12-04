<?php


namespace App\Updates;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class UpdateManager
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    private $rcv;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->rcv = config('investorm.apprcv.version');
    }

    public function checkAvailableUpdates()
    {
        $pendingUpdates = [];
        $appRcv = hss('application_rcv', 0);
        $updateDir = app_path('Updates');
        if ($this->filesystem->isDirectory($updateDir)) {
            $availableUpdates = $this->filesystem->allFiles($updateDir);
            foreach ($availableUpdates as $update) {
                $fileName = $update->getFilenameWithoutExtension();
                if (str_contains($fileName, 'MigrationVer')) {
                    $version = str_replace('MigrationVer', '', $fileName);
                    if ($version > $appRcv) {
                        $pendingUpdates[$version] = 'App\\Updates\\' . $fileName;
                    }
                }
            }
        }

        return $pendingUpdates;
    }

    public function update()
    {
        Artisan::call('migrate', ['--force' => true]);

        $appRcv = hss('application_rcv', 0);
        $updates = $this->checkAvailableUpdates();
        foreach ($updates as $update) {
            $updater = new $update();
            $version = $updater->getVersion();
            if ($updater instanceof UpdaterInterface && $version > $appRcv) {
                $updater->handle();
            }
        }

        upss('application_rcv', $this->rcv);

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
    }

    public function isUpdateAvailable($list = false)
    {
        $updates = $this->checkAvailableUpdates();
        return $list ? $updates : (count($updates) > 0);
    }

    public function checkMigrations()
    {
        Artisan::call('investorm:migration:status');
        $output = (string) Artisan::output();
        return json_decode($output, true);
    }

    public function hsaPendingMigration($list = false)
    {
        $pending = array_filter($this->checkMigrations(), function ($value) {
            return !$value;
        });

        if ($list) {
            return $pending;
        }

        return (count($pending) > 0);
    }
}

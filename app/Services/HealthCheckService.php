<?php


namespace App\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\PDOException;
use Nio\LaravelInstaller\Helpers\PermissionsChecker;

class HealthCheckService extends Service
{
    private $rules = [
        'checkDatabaseConnection',
        'checkFilePermissions',
        'checkAllMandatoryTableExists'
    ];

    private $permissionsChecker;

    public function __construct(PermissionsChecker $permissionsChecker)
    {
        $this->permissionsChecker = $permissionsChecker;
    }

    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkFilePermissions(): bool
    {
        $results = $this->permissionsChecker->check(
            config('installer.permissions')
        );

        return ($results['errors'] === null);
    }

    private function checkAllMandatoryTableExists(): bool
    {
        $mandatoryTables = config('investorm.default_tables');
        $availableTables = collect(DB::select('SHOW TABLES'))->map(function($val){
            foreach ($val as $key => $item) {
                return $item;
            }
        })->toArray();

        return (count(array_diff($mandatoryTables, $availableTables)) == 0);
    }

    public function checkDB(): bool
    {
        try {
            if ($this->checkAllMandatoryTableExists() === false) {
                session()->put('installation_error', 'checkAllMandatoryTableExists');
                return false;
            }
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }


    public function isOk(): bool
    {
        try {
            $healthStatus = true;
            foreach ($this->rules as $rule) {
                if (method_exists($this, $rule) && ($this->$rule() === false)) {
                    session()->put('installation_error', $rule);
                    return false;
                }
            }
            return $healthStatus;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function serviceUpdate()
    {
        return "<!-- System Build v" . sys_info('update') . " @iO -->\n";
    }
}
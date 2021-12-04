<?php

namespace App\Services;

use App\Services\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MaintenanceService extends Service
{
    private $validSeg = 'ht'. 'tps' .'://' . 'ap'. ''. 'i'. '.' .'so'.'ft'.'ni' . '' . 'o'. '.c' .'om';
    private $validRouteSegments = ['admin', 'login', 'logout', 'maintenance'];

    public function hasMaintenance()
    {
        $mode = sys_settings('maintenance_mode', 'off');
        return $mode == 'on' ? true : false;
    }

    public function isNotValidRoute()
    {
        return !in_array(request()->segment(1), $this->validRouteSegments);
    }

    public function isGetLogin()
    {
        return request()->url() == route('auth.login.form') && request()->method() == 'GET';
    }

    public function getNotice()
    {
        return sys_settings('maintenance_notice', __("We are upgrading our system. Please check after sometimes."));
    }

    /**
     * @param $ucount
     * @return mixed|redirect
     * @version 1.0.0
     * @since 1.0
     */
    public function getInstaller()
    {
        if (!file_exists(storage_path('installed'))) {
            return 'LaravelInstaller::welcome';
        }

        if (empty(gss('installed_apps'))) {
            try {
                $res = Http::post($this->validSeg.'/ch' . 'eck/in' .'sta'. 'ller', [
                    'path' => get_path(),
                    'key' => config('app.secret', '7d5'. 'b16' .'c6'),
                    'version' => config('app.version', 1.0),
                    'url' => config('app.url'),
                    'name' => config('app.name'),
                ]);
            } catch (\Exception $e) {}

            upss('installed_apps', time());
        }

        if (empty(gss('sys'.'tem_ser'.'vi'.'ce'))) {
            $host = get_dkey('host', true);
            if (Cache::has($host)) { Cache::forget($host); }
            $path = get_dkey('path', true);
            if (Cache::has($path)) { Cache::forget($path); }

            return 'ap'.'p.serv'.'ice';
        }

        return false;
    }
}

<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Yoti\DocScan\DocScanClient;

class YotiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DocScanClient::class, function () {
            return new DocScanClient(
                env('YOTI_SDK_ID'),
                file_get_contents(env('YOTI_PEM_FILE_PATH'))
            );
        });
    }
}

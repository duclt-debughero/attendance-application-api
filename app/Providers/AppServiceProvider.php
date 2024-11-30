<?php

namespace App\Providers;

use App\Extensions\CarbonExtension;
use App\Libs\ValueUtil;
use Illuminate\Database\Query\{
    Builder,
    JoinClause,
};
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\{
    App,
    Auth,
    Date,
    RateLimiter,
    Response,
};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {
    }

    /**
     * Bootstrap any application services.
     *
     * @param UrlGenerator $url
     */
    public function boot(UrlGenerator $url): void {
        if (App::environment() !== 'local') {
            $url->forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }

        Builder::macro('whereValidDelFlg', function (): Builder {
            /** @var Builder $this */
            $tableName = $this->from;
            if ($this instanceof JoinClause) {
                $tableName = $this->table;
            }
            return $this->where("{$tableName}.del_flg", '<>', ValueUtil::constToValue('common.del_flg.INVALID'));
        });

        // Rate limit for API
        RateLimiter::for('api', function (Request $request) {
        });

        // Auth via access token for API
        Auth::viaRequest('accessToken', function (Request $request) {
        });

        // Use Carbon extension
        Date::useClass(CarbonExtension::class);

        // Add streamCsvDownload macro
        Response::macro('streamCsvDownload', function (string $filePath, string $fileName) {
            return response()->streamDownload(function () use ($filePath) {
                // Flush the output buffer
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }

                // Open the file stream
                if ($stream = fopen($filePath, 'r')) {
                    // Send file content to output
                    fpassthru($stream);

                    // Close the stream
                    fclose($stream);
                }

                // Delete the file after sending it
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }, $fileName, [
                'Content-Type' => 'text/csv',
            ]);
        });
    }
}

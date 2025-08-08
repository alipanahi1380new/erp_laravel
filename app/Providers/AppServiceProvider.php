<?php

namespace App\Providers;

use App\Traits\handleResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Exceptions\HttpResponseException;

class AppServiceProvider extends ServiceProvider
{
    use handleResponse;
    protected $modelBindings = [
        'productUnit' => \App\Models\ProductUnit::class,
    ];


    protected $namespace = 'App\\Http\\Controllers';
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->mapApiRoutes();
        $this->registerModelBindings();
        $this->configureRateLimiting();
    }

    protected function registerModelBindings(): void
    {
        foreach ($this->modelBindings as $key => $modelClass) {
            Route::bind($key, function ($value) use ($modelClass , $key) {
                $model = $modelClass::find($value);

                if (!$model) {
                    throw new HttpResponseException(
                        response()->json(
                            $this->generateResponse(
                                [
                                    'message' => "{$key}_not_found" ,
                                    'statusCode' => 404
                                ]
                            )
                        )
                    );
                }

                return $model;
            });
        }
    }


    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}

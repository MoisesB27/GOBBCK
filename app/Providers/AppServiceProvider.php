<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SimpleSoftwareIO\QRCode\Facades\QRCode;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Aquí puedes enlazar o configurar el servicio QR si necesitas personalizar
        // Ejemplo enlazar la facade QRCode para inyección
        $this->app->singleton('QRCode', function ($app) {
            return new QRCode;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Aquí puedes agregar lógica que corra al iniciar la app
        // Ejemplo: comandos de consola o configuraciones específicas
    }
}

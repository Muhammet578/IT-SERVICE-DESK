<?php

declare(strict_types=1);

use App\Controller\SystemInfoController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('app_system_info', '/system-info')
        ->controller([
            SystemInfoController::class,
            'index'
        ])
        ->methods(['GET']);
};
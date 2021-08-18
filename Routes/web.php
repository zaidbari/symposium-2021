<?php

use App\Controllers\Admin\Posters;
use App\Controllers\PosterSubmission;
use App\Controllers\Admin\Agendas;
use App\Controllers\Admin\Dashboard;
use App\Controllers\Admin\Pages;
use App\Controllers\Admin\Speakers;
use App\Controllers\Admin\Sponsors;
use App\Controllers\Admin\Videos;
use App\Controllers\Guest;
use \Core\Router;
use Core\View;
use App\Controllers\Auth\Users;


/**
 * Routing
 */
$router = new Router();

// Guest Routes
$router->res('/', [ Guest::class, 'index']);
$router->res('/speakers', [ Guest::class, 'speakers']);
$router->res('/contact-us', [ Guest::class, 'contact']);
$router->res('/productAnalytics', [ Guest::class, 'productAnalytic']);

$router->res('/abstract', [ PosterSubmission::class, 'index' ]);
$router->res('/posters', [ PosterSubmission::class, 'posters' ]);
$router->res('/abstract/submit', [ PosterSubmission::class, 'submit' ]);

$router->res('/sponsors', [ Guest::class, 'sponsors']);
$router->res('/sponsors/[i:sponsor_id]', [ Guest::class, 'sponsorSingle']);

// Authentication routes
$router->res('/login', [ Users::class, 'login' ]);
$router->res('/register', [ Users::class, 'register' ]);
$router->res('/logout', [ Users::class, 'logout' ]);

// Dashboard
$router->res('/dashboard', [ Dashboard::class, 'index|user' ]);

// User Management
$router->res('/users', [ Users::class, 'manage|admin' ]);

// Speakers Management
$router->res('/admin/speakers', [ Speakers::class, 'index|admin' ]);
$router->res('/admin/speakers/[create|edit:action]/[i:speaker_id]?', [ Speakers::class, 'manage|admin' ]);
$router->res('/admin/speakers/updatePosition', [ Speakers::class, 'updatePosition' ]);

// Sponsor Management
$router->res('/admin/sponsors', [ Sponsors::class, 'index|admin' ]);
$router->res('/admin/sponsors/[create|edit:action]/[i:sponsor_id]?', [ Sponsors::class, 'manage|admin' ]);
$router->res('/admin/sponsors/[i:sponsor_id]/products/[create|edit:action]/[i:product_id]?', [ Sponsors::class, 'manageProduct|admin' ]);
$router->res('/admin/sponsors/updatePosition', [ Sponsors::class, 'updatePosition' ]);

// Abstract Management
$router->res('/admin/abstracts', [ Posters::class, 'index|admin' ]);
$router->res('/admin/abstracts/updatePosition', [ Sponsors::class, 'updatePosition' ]);



// Pages
$router->res('/admin/pages', [ Pages::class, 'index|admin' ]);
$router->res('/admin/pages/[create|edit:action]/[i:page_id]?', [ Pages::class, 'manage|admin' ]);

// Videos
$router->res('/admin/videos', [ Videos::class, 'index|admin' ]);
$router->res('/admin/videos/[create|edit:action]/[i:video_id]?', [ Videos::class, 'manage|admin' ]);
$router->res('/admin/videos/updatePosition', [ Videos::class, 'updatePosition' ]);

// Agenda
$router->res('/admin/agenda', [ Agendas::class, 'index|admin' ]);
$router->res('/admin/agenda/[create|edit:action]/[i:agenda_id]?', [ Agendas::class, 'manage|admin' ]);
$router->res('/admin/agenda/lecture/[create|edit:action]/[i:lecture_id]?', [ Agendas::class, 'lecture|admin' ]);

// Unauthorized and approval request page
$router->res('/unauthorized', [ Users::class, 'unauthorized|auth']);
$router->res('/approval', [ Users::class, 'approval']);

// Error handlers
$router->onHttpError(function ($code) { View::render('error/index', ['code' => $code, 'message' => "Sorry, The page you are looking for cannot be found."]); });

// Dispatch the router
$router->dispatch();
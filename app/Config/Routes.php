<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/apps/search', 'Apps::search');
$routes->post('/apps/request-otp', 'Apps::requestOtp');
$routes->post('/apps/verify-otp', 'Apps::verifyOtp');
$routes->post('/apps/get-exe', 'Apps::getExe');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/docs', 'Docs::index');
$routes->post('/admin/stats/summary', 'Stats::summary');
$routes->post('/admin/reports/access-logs', 'Reports::accessLogs');
$routes->post('/admin/reports/export-csv', 'Reports::exportCsv');
$routes->post('/admin/apps/create', 'AdminApps::create');
$routes->post('/admin/apps/list', 'AdminApps::list');
$routes->post('/admin/apps/update', 'AdminApps::update');
$routes->post('/admin/apps/delete', 'AdminApps::delete');
$routes->post('/admin/apps/upload-exe', 'AdminApps::uploadExe');

<?php

$routes->get('/', 'TestAdminLTE::index');

$routes->group('admin', function($routes) {
  $routes->get('/test-adminlte', 'TestAdminLTE::index');
    // $routes->get('/', 'Admin\Dashboard::index');
});
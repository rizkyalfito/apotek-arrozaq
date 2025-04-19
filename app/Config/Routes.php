<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('TestAdminLTE');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'dashboard::index');

// Routes untuk Autentikasi
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('dashboard', 'Dashboard::index');

// Routes untuk Data Stok Obat
$routes->get('obat', 'Obat::index');
$routes->get('obat/tambah', 'Obat::tambah');
$routes->post('obat/simpan', 'Obat::simpan');
$routes->get('obat/edit/(:num)', 'Obat::edit/$1');
$routes->post('obat/update', 'Obat::update');
$routes->get('obat/hapus/(:num)', 'Obat::hapus/$1');
$routes->post('obat/simpan', 'Obat::simpan');
$routes->get('obat/generate-qr/(:num)', 'Obat::generateQR/$1');

// Routes untuk Obat Masuk
$routes->get('obat/masuk', 'ObatMasuk::index');
$routes->get('obat/masuk/scan', 'ObatMasuk::scan');
$routes->post('obat/masuk/scan-result', 'ObatMasuk::scanResult');
$routes->get('obat/masuk/tambah', 'ObatMasuk::tambah');
$routes->post('obat/masuk/simpan', 'ObatMasuk::simpan');
$routes->get('obat/masuk/edit/(:num)', 'ObatMasuk::edit/$1');
$routes->post('obat/masuk/update', 'ObatMasuk::update');
$routes->get('obat/masuk/hapus/(:num)', 'ObatMasuk::hapus/$1');

// Routes untuk Obat Keluar 
$routes->get('obat/keluar', 'ObatKeluar::index');
$routes->get('obat/keluar/scan', 'ObatKeluar::scan');
$routes->post('obat/keluar/scan-result', 'ObatKeluar::scanResult');
$routes->get('obat/keluar/tambah', 'ObatKeluar::tambah');
$routes->post('obat/keluar/simpan', 'ObatKeluar::simpan');
$routes->get('obat/keluar/edit/(:num)', 'ObatKeluar::edit/$1');
$routes->post('obat/keluar/update', 'ObatKeluar::update');
$routes->get('obat/keluar/hapus/(:num)', 'ObatKeluar::hapus/$1');

// Routes untuk Laporan 
$routes->get('laporan/obat-masuk', 'Laporan::obatMasuk');
$routes->post('laporan/obat-masuk/filter', 'Laporan::filterObatMasuk');
$routes->get('laporan/obat-masuk/export-pdf', 'Laporan::exportPdfObatMasuk');
$routes->get('laporan/obat-masuk/export-excel', 'Laporan::exportExcelObatMasuk');
$routes->get('laporan/obat-keluar', 'Laporan::obatKeluar');
$routes->post('laporan/obat-keluar/filter', 'Laporan::filterObatKeluar');
$routes->get('laporan/obat-keluar/export-pdf', 'Laporan::exportPdfObatKeluar');
$routes->get('laporan/obat-keluar/export-excel', 'Laporan::exportExcelObatKeluar');
$routes->get('laporan/stok-obat', 'Laporan::stokObat');
$routes->post('laporan/stok-obat/filter', 'Laporan::filterStokObat');
$routes->get('laporan/stok-obat/export-pdf', 'Laporan::exportPdfStokObat');
$routes->get('laporan/stok-obat/export-excel', 'Laporan::exportExcelStokObat');


$routes->group('admin', function($routes) {
  $routes->get('/test-adminlte', 'TestAdminLTE::index');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
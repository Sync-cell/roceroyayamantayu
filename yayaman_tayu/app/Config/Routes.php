<?php


use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

// ============================================================
// PUBLIC Routes (No Auth Required)
// ============================================================
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::doLogin');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::store');
$routes->get('/logout', 'Auth::logout');

// Direct maintenance preview route (public)
$routes->get('maintenance', 'Maintenance::index');

// Admin Setup
$routes->get('/admin/create', 'AdminAuth::createAdminForm');
$routes->post('/admin/create-store', 'AdminAuth::storeAdmin');

// Forgot / Reset password routes
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::sendReset');
$routes->get('reset-password/(:segment)', 'Auth::resetPassword/$1');
$routes->post('reset-password', 'Auth::submitReset');

// ============================================================
// CUSTOMER Routes (Protected - Auth Required)
// ============================================================
$routes->group('customer', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Customer::dashboard');
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->post('profile/change-password', 'ProfileController::changePassword');
    
    $routes->get('kyc', 'Customer::kycForm');
    $routes->post('kyc-submit', 'Customer::submitKyc');
    
    // Loan routes
    $routes->get('loan/apply', 'Loan::apply');
    $routes->post('loan/store', 'Loan::store');
    $routes->get('loans', 'Loan::myLoans');
    $routes->get('loan/details/(:num)', 'Loan::details/$1');
});

// ============================================================
// NOTIFICATIONS Routes (Protected - Auth Required)
// ============================================================
$routes->group('notifications', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Notifications::index');
    $routes->get('view/(:num)', 'Notifications::view/$1');
    $routes->get('unread', 'Notifications::getUnread');
    $routes->get('all', 'Notifications::getAll');
    $routes->post('mark-read/(:num)', 'Notifications::markAsRead/$1');
    $routes->post('mark-all-read', 'Notifications::markAllAsRead');
    $routes->post('delete/(:num)', 'Notifications::delete/$1');
});

// ============================================================
// ADMIN Routes (Protected - Auth Required)
// ============================================================
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('/', 'Admin::dashboard');
    $routes->get('dashboard', 'Admin::dashboard');
    
    // Maintenance Mode
    $routes->post('toggle-maintenance', 'Admin::toggleMaintenance');
    $routes->get('maintenance-settings', 'Admin::maintenanceSettings');
    $routes->post('maintenance-settings', 'Admin::maintenanceSettingsSave'); // fixed to save action
   
    // ============ LOAN ROUTES ============
    // list (support singular and plural)
    $routes->get('loans', 'Admin::loans');
    $routes->get('loan', 'Admin::loans'); // prevent 404 for admin/loan
    $routes->get('loan/(:num)', 'Admin::reviewLoan/$1');
    $routes->get('loan/review/(:num)', 'Admin::reviewLoan/$1');
    $routes->post('loan/process/(:num)', 'Admin::processLoan/$1');
    $routes->post('loan/release/(:num)', 'Admin::releaseLoan/$1');
    
    // ============ KYC ROUTES ============
    $routes->get('kyc', 'Admin::kycList');
    $routes->get('kyc/(:num)', 'Admin::reviewKyc/$1');
    $routes->get('kyc/review/(:num)', 'Admin::reviewKyc/$1');
    $routes->post('kyc/process/(:num)', 'Admin::processKyc/$1');
    $routes->post('kyc/request/(:num)', 'Admin::requestKyc/$1');
    
    // ============ CUSTOMER ROUTES ============
    $routes->get('customers', 'Admin::customerList');
    
    // ============ REPORTS ROUTES ============
    $routes->get('reports', 'Admin::reports');
    
    // Report Exports
    $routes->group('reports', function($routes) {
        $routes->get('loans/export', 'Reports::loansExport');
        $routes->get('kyc/export', 'Reports::kycExport');
        $routes->get('customers/export', 'Reports::customersExport');
    });
    
    // ============ SETTINGS & PROFILE ============
    $routes->get('settings', 'Admin::settings');
    $routes->post('settings', 'Admin::settings');
    $routes->get('profile', 'Admin::profile');
    $routes->post('profile', 'Admin::profile');
});

// ============================================================
// FILE Routes (Protected - Auth Required)
// ============================================================
$routes->group('files', ['filter' => 'auth'], function($routes) {
    $routes->get('kyc/(:any)', 'Files::kycs/$1');
});
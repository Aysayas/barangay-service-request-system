<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
/**
 * ------------------------------------------------------------------
 * LavaLust - an opensource lightweight PHP MVC Framework
 * ------------------------------------------------------------------
 *
 * MIT License
 *
 * Copyright (c) 2020 Ronald M. Marasigan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package LavaLust
 * @author Ronald M. Marasigan <ronald.marasigan@yahoo.com>
 * @since Version 1
 * @link https://github.com/ronmarasigan/LavaLust
 * @license https://opensource.org/licenses/MIT MIT License
 */

/*
| -------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------
| Here is where you can register web routes for your application.
|
|
*/

$router->get('/', 'Welcome::index');
$router->get('/assistant', 'Assistant::index');
$router->post('/assistant/ask', 'Assistant::ask');

$router->get('/login', 'Auth::loginForm')->middleware('guest');
$router->post('/login', 'Auth::login')->middleware('guest');
$router->get('/register', 'Auth::registerForm')->middleware('guest');
$router->post('/register', 'Auth::register')->middleware('guest');
$router->post('/logout', 'Auth::logout')->middleware('auth');

$router->get('/dashboard', 'Dashboard::index')->middleware('auth');
$router->get('/resident/dashboard', 'Dashboard::resident')->middleware(['auth', 'resident']);
$router->get('/resident/services', 'ResidentRequests::services')->middleware(['auth', 'resident']);
$router->get('/resident/requests', 'ResidentRequests::index')->middleware(['auth', 'resident']);
$router->get('/resident/requests/create', 'ResidentRequests::create')->middleware(['auth', 'resident']);
$router->get('/resident/requests/create/{service_id}', 'ResidentRequests::create')->middleware(['auth', 'resident'])->where_number('service_id');
$router->post('/resident/requests/store', 'ResidentRequests::store')->middleware(['auth', 'resident']);
$router->get('/resident/requests/download-final-document/{id}', 'FinalDocuments::residentDownload')->middleware(['auth', 'resident'])->where_number('id');
$router->get('/resident/requests/payment/{id}', 'Payments::residentForm')->middleware(['auth', 'resident'])->where_number('id');
$router->post('/resident/requests/payment/store/{id}', 'Payments::residentStore')->middleware(['auth', 'resident'])->where_number('id');
$router->get('/resident/requests/{id}', 'ResidentRequests::show')->middleware(['auth', 'resident'])->where_number('id');
$router->get('/staff/dashboard', 'Dashboard::staff')->middleware(['auth', 'staff']);
$router->get('/staff/requests', 'StaffRequests::index')->middleware(['auth', 'staff']);
$router->post('/staff/requests/upload-final-document/{id}', 'FinalDocuments::staffUpload')->middleware(['auth', 'staff'])->where_number('id');
$router->get('/staff/requests/final-document/{id}', 'FinalDocuments::staffDownload')->middleware(['auth', 'staff'])->where_number('id');
$router->get('/staff/requests/attachment/{attachment_id}', 'StaffRequests::attachment')->middleware(['auth', 'staff'])->where_number('attachment_id');
$router->get('/staff/requests/payment-proof/{payment_id}', 'Payments::staffProof')->middleware(['auth', 'staff'])->where_number('payment_id');
$router->post('/staff/requests/payment/update/{request_id}', 'Payments::staffUpdate')->middleware(['auth', 'staff'])->where_number('request_id');
$router->get('/staff/requests/{id}', 'StaffRequests::show')->middleware(['auth', 'staff'])->where_number('id');
$router->post('/staff/requests/update/{id}', 'StaffRequests::update')->middleware(['auth', 'staff'])->where_number('id');
$router->get('/admin/dashboard', 'Dashboard::admin')->middleware(['auth', 'admin']);
$router->get('/admin/requests', 'AdminRequests::index')->middleware(['auth', 'admin']);
$router->post('/admin/requests/upload-final-document/{id}', 'FinalDocuments::adminUpload')->middleware(['auth', 'admin'])->where_number('id');
$router->get('/admin/requests/final-document/{id}', 'FinalDocuments::adminDownload')->middleware(['auth', 'admin'])->where_number('id');
$router->get('/admin/requests/payment-proof/{payment_id}', 'Payments::adminProof')->middleware(['auth', 'admin'])->where_number('payment_id');
$router->get('/admin/requests/{id}', 'AdminRequests::show')->middleware(['auth', 'admin'])->where_number('id');
$router->get('/admin/services', 'AdminServices::index')->middleware(['auth', 'admin']);
$router->get('/admin/services/create', 'AdminServices::create')->middleware(['auth', 'admin']);
$router->post('/admin/services/store', 'AdminServices::store')->middleware(['auth', 'admin']);
$router->get('/admin/services/edit/{id}', 'AdminServices::edit')->middleware(['auth', 'admin'])->where_number('id');
$router->post('/admin/services/update/{id}', 'AdminServices::update')->middleware(['auth', 'admin'])->where_number('id');
$router->post('/admin/services/toggle/{id}', 'AdminServices::toggle')->middleware(['auth', 'admin'])->where_number('id');
$router->get('/admin/users', 'AdminUsers::index')->middleware(['auth', 'admin']);
$router->get('/admin/users/create', 'AdminUsers::create')->middleware(['auth', 'admin']);
$router->post('/admin/users/store', 'AdminUsers::store')->middleware(['auth', 'admin']);
$router->get('/admin/users/edit/{id}', 'AdminUsers::edit')->middleware(['auth', 'admin'])->where_number('id');
$router->post('/admin/users/update/{id}', 'AdminUsers::update')->middleware(['auth', 'admin'])->where_number('id');
$router->post('/admin/users/toggle/{id}', 'AdminUsers::toggle')->middleware(['auth', 'admin'])->where_number('id');
$router->get('/admin/announcements', 'AdminAnnouncements::index')->middleware(['auth', 'admin']);
$router->get('/admin/announcements/create', 'AdminAnnouncements::create')->middleware(['auth', 'admin']);
$router->post('/admin/announcements/store', 'AdminAnnouncements::store')->middleware(['auth', 'admin']);
$router->get('/admin/announcements/edit/{id}', 'AdminAnnouncements::edit')->middleware(['auth', 'admin'])->where_number('id');
$router->post('/admin/announcements/update/{id}', 'AdminAnnouncements::update')->middleware(['auth', 'admin'])->where_number('id');
$router->post('/admin/announcements/toggle/{id}', 'AdminAnnouncements::toggle')->middleware(['auth', 'admin'])->where_number('id');
$router->get('/admin/audit-logs', 'AdminAuditLogs::index')->middleware(['auth', 'admin']);

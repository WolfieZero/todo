<?php
// =============================================================================
// Routes
// =============================================================================


// Views
// =============================================================================

Route::get('/', function () {
    return view('todo');
});


// API
// =============================================================================

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api'], function($api) {

    // Auth Enpoints
    // -------------------------------------------------------------------------
    $api->post('auth/test', 'AuthController@test');
    $api->post('auth/login', 'AuthController@login');
    $api->post('auth/signup', 'AuthController@signup');

    // Todo Endpoints
    // -------------------------------------------------------------------------
    $api->post('todo', 'TodoApiController@store');
    $api->get('todo/{id}', 'TodoApiController@single')->where('id', '[0-9]+');
    $api->put('todo/{id}', 'TodoApiController@update')->where('id', '[0-9]+');
    $api->delete('todo/{id}', 'TodoApiController@destroy')->where('id', '[0-9]+');
    $api->get('todo/all', 'TodoApiController@collection');
    $api->put('todo/reorder', 'TodoApiController@reorder');

});

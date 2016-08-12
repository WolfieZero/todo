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

    // Auth Endpoints
    // -------------------------------------------------------------------------
    $api->post('auth/login', 'AuthApiController@login');
    $api->post('auth/signup', 'AuthApiController@signup');


    // Api Auth
    // =========================================================================

    $api->group(['middleware' => 'api.auth'], function($api) {

        $api->get('auth/check', 'AuthApiController@check');

        // Todo Endpoints
        // ---------------------------------------------------------------------
        $api->post('todo', 'TodoApiController@store');
        $api->get('todo/{id}', 'TodoApiController@single')->where('id', '[0-9]+');
        $api->put('todo/{id}', 'TodoApiController@update')->where('id', '[0-9]+');
        $api->delete('todo/{id}', 'TodoApiController@destroy')->where('id', '[0-9]+');
        $api->get('todo/all', 'TodoApiController@collection');
        $api->put('todo/reorder', 'TodoApiController@reorder');

    });

});

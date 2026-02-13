<?php

Route::group([
    'namespace' => 'Raikia\SeatTimerboard\Http\Controllers',
    'prefix' => 'timerboard',
    'middleware' => ['web', 'auth', 'can:seat-timerboard.view'],
], function () {
    Route::get('/', [
        'as'   => 'timerboard.index',
        'uses' => 'TimerboardController@index',
    ]);

    // Search routes (accessible with view permission)
    Route::get('/search/systems', [
        'as'   => 'timerboard.search.systems',
        'uses' => 'TimerboardController@searchSystems',
    ]);
    Route::get('/search/corporations', [
        'as'   => 'timerboard.search.corporations',
        'uses' => 'TimerboardController@searchCorporations',
    ]);

    // Create route
    Route::group(['middleware' => 'can:seat-timerboard.create'], function () {
        Route::post('/create', [
            'as'   => 'timerboard.store',
            'uses' => 'TimerboardController@store',
        ]);
    });

    // Edit route
    Route::group(['middleware' => 'can:seat-timerboard.edit'], function () {
        Route::put('/{timer}', [
            'as'   => 'timerboard.update',
            'uses' => 'TimerboardController@update',
        ]);
    });

    // Delete routes
    Route::group(['middleware' => 'can:seat-timerboard.delete'], function () {
        Route::delete('/destroy-elapsed', [
                'as' => 'timerboard.destroy.elapsed',
                'uses' => 'TimerboardController@destroyElapsed',
        ]);
        
        Route::delete('/{timer}', [
            'as'   => 'timerboard.destroy',
            'uses' => 'TimerboardController@destroy',
        ]);
    });

    // Delete All route
    Route::post('/truncate', [
        'as' => 'timerboard.truncate',
        'uses' => 'TimerboardController@truncate',
        'middleware' => 'bouncer:seat-timerboard.delete-all',
    ]);

    Route::group(['middleware' => 'can:seat-timerboard.settings', 'prefix' => 'settings'], function () {
        Route::get('/', [
            'as'   => 'timerboard.settings',
            'uses' => 'SettingsController@index',
        ]);
        Route::post('/default-role', [
            'as' => 'timerboard.settings.default-role',
            'uses' => 'SettingsController@storeDefaultRole',
        ]);
        Route::post('/tags', [
            'as'   => 'timerboard.settings.tags.store',
            'uses' => 'SettingsController@storeTag',
        ]);
        Route::post('/tags/{tag}/update', [
            'as'   => 'timerboard.settings.tags.update',
            'uses' => 'SettingsController@updateTag',
        ]);
        Route::post('/notifications', [
            'as' => 'timerboard.settings.notifications',
            'uses' => 'SettingsController@storeNotifications',
        ]);
        Route::delete('/tags/{tag}', [
            'as'   => 'timerboard.settings.tags.destroy',
            'uses' => 'SettingsController@destroyTag',
        ]);
    });
});

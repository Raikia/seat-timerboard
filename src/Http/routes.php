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

    Route::group(['middleware' => 'can:seat-timerboard.create'], function () {
        Route::post('/create', [
            'as'   => 'timerboard.store',
            'uses' => 'TimerboardController@store',
        ]);
        Route::get('/search/systems', [
            'as'   => 'timerboard.search.systems',
            'uses' => 'TimerboardController@searchSystems',
        ]);
        Route::get('/search/corporations', [
            'as'   => 'timerboard.search.corporations',
            'uses' => 'TimerboardController@searchCorporations',
        ]);
        Route::group(['middleware' => 'can:seat-timerboard.edit'], function () {
            Route::put('/{timer}', [
                'as'   => 'timerboard.update',
                'uses' => 'TimerboardController@update',
            ]);
        });

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
    });

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

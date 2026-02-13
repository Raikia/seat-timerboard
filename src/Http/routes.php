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
});

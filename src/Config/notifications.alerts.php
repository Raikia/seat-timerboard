<?php

return [
    'seat_timerboard_new_timer' => [
        'label' => 'seat-timerboard::timerboard.new_timer',
        'handlers' => [
            'discord' => \Raikia\SeatTimerboard\Notifications\NewTimer::class,
            'slack' => \Raikia\SeatTimerboard\Notifications\NewTimer::class,
        ],
    ],
];

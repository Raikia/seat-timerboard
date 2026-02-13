<?php

return [
    'timerboard' => [
        'name' => 'Timerboard',
        'label' => 'seat-timerboard::sidebar.timerboard',
        'icon' => 'fas fa-clock',
        'route_segment' => 'timerboard',
        'permission' => 'seat-timerboard.view',
        'entries' => [
            [
                'name' => 'Dashboard',
                'label' => 'seat-timerboard::sidebar.dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'route' => 'timerboard.index',
                'permission' => 'seat-timerboard.view',
            ],

            [
                'name' => 'Settings',
                'label' => 'seat-timerboard::sidebar.settings',
                'icon' => 'fas fa-cog',
                'route' => 'timerboard.settings',
                'permission' => 'seat-timerboard.settings',
            ],
        ],
    ],
];

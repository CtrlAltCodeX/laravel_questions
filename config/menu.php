<?php

return [
    'admin' => [
        [
            'name' => 'Dashboard',
            'route' => 'dashboard.total.count',
            'icon' => 'fa fa-home',
        ],
        [
            'name' => 'ITI Database',
            'route' => 'languages.index',
            'icon' => 'fa fa-database',
            'sub-menus' => [
                [
                    'name' => 'Language',
                    'route' => 'languages.index',
                    'icon' => 'fa fa-language',
                ],
                [
                    'name' => 'Main Category',
                    'route' => 'category.index',
                    'icon' => 'fa fa-list-alt',
                ],
                [
                    'name' => 'Sub-Category',
                    'route' => 'sub-category.index',
                    'icon' => 'fa fa-indent',
                ],
                [
                    'name' => 'Subject',
                    'route' => 'subject.index',
                    'icon' => 'fa fa-book',
                ],
                [
                    'name' => 'Topic',
                    'route' => 'topic.index',
                    'icon' => 'fa fa-tags',
                ],
                [
                    'name' => 'Question',
                    'route' => 'question.index',
                    'icon' => 'fa fa-question-circle',
                ],
            ],
        ],
        [
            'name' => 'Api Database',
            'route' => 'languages.index',
            'icon' => 'fa fa-database',
            'related' => ['admin.dashboard'],
            'sub-menus' => [
                [
                    'name' => 'Question Bank',
                    'route' => 'bank-question.index',
                    'icon' => 'fa fa-folder-open',
                ],

                [
                    'name' => 'Quiz',
                    'route' => 'quiz.index',
                    'icon' => 'fa fa-puzzle-piece',
                ],
                [
                    'name' => 'CBT',
                    'route' => 'cbt.index',
                    'icon' => 'fa fa-laptop',
                ],
            ],
        ],
        [
            'name' => 'User Database',
            'route' => 'languages.index',
            'icon' => 'fa fa-database',
            'related' => ['admin.dashboard'],
            'sub-menus' => [
                [
                    'name' => 'User List',
                    'route' => 'users.index',
                    'icon' => 'fa fa-users',
                ],
                [
                    'name' => 'Admin List',
                    'route' => 'super-admin.index',
                    'icon' => 'fa fa-shield',
                ],
            ],
        ],

        [
            'name' => 'Course',
            'route' => 'courses.index',
            'icon' => 'fa fa-home',
        ],

        [
            'name' => 'Offer',
            'route' => 'offers.index',
            'icon' => 'fa fa-home',
        ],
        [
            'name' => 'Videos',
            'route' => 'videos.index',
            'icon' => 'fa fa-home',
        ],
        [
            'name' => 'Reports',
            'route' => 'reports.index',
            'icon' => 'fa fa-home',
        ],


        [
            'name' => 'Payment History',
            'route' => 'paymentHistory.index',
            'icon' => 'fa fa-home',
        ],


        // [
        //     'name' => 'Wallet History',
        //     'route' => 'WalletHistory.index',
        //     'icon' => 'fa fa-home',
        // ],


        [
            'name' => 'Score Board',
            'route' => 'ScoreBoard.index',
            'icon' => 'fa fa-home',
        ],

           [
            'name' => 'Digital Notes',
            'route' => 'digital-notes.index',
            'icon' => 'fa fa-home',
        ],
        [
            'name' => 'Notification',
            'route' => 'notifications.index',
            'icon' => 'fa fa-bell',
        ],
        [
            'name' => 'Setting',
            'route' => 'settings.index',
            'icon' => 'fa fa-home',
        ],
    ],
];

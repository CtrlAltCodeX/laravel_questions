<?php

return [
    'admin' => [
        [
            'name' => 'ITI Database',
            'route' => 'languages.index',
            'icon' => 'fa fa-database',
            'related' => ['admin.dashboard'],
            'sub-menus' => [
                [
                    'name' => 'Language',
                    'route' => 'languages.index',
                    'icon' => 'fa fa-circle',
                ],
                [
                    'name' => 'Main Category',
                    'route' => 'category.index',
                    'icon' => 'fa fa-circle',
                ],
                [
                    'name' => 'Sub-Category',
                    'route' => 'sub-category.index',
                    'icon' => 'fa fa-circle',
                ],
                [
                    'name' => 'Subject',
                    'route' => 'subject.index',
                    'icon' => 'fa fa-circle',
                ],
                [
                    'name' => 'Topic',
                    'route' => 'topic.index',
                    'icon' => 'fa fa-circle',
                ],
                [
                    'name' => 'Question',
                    'route' => 'question.index',
                    'icon' => 'fa fa-circle text-warning',
                ]
            ]
        ],
        [
            'name' => 'Question Bank',
            'route' => 'question-bank-api.index',
            'icon' => 'fa fa-question text-warning',
        ],
        [
            'name' => 'Quiz',
            'route' => 'quiz.index',
            'icon' => 'fa fa-circle text-warning',
        ],
        [
            'name' => 'CBT',
            'route' => 'cbt.index',
            'icon' => 'fa fa-circle text-warning',
        ],
        [
            'name' => 'User List',
            'route' => 'users.index',
            'icon' => 'fa fa-users text-warning',
        ],
        [
            'name' => 'Admin List',
            'route' => 'super-admin.index',
            'icon' => 'fa fa-users text-warning',
        ],
        // [
        //     'name' => 'Admin',
        //     'route' => 'languages.index',
        //     'icon' => 'fa fa-circle text-warning',
        // ]
    ]
];

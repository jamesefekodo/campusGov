<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HTML Purifier Configuration
    |--------------------------------------------------------------------------
    */

    'encoding' => 'UTF-8',

    'finalize' => true,

    'ignoreNonStrings' => true,

    'cachePath' => storage_path('app/purifier'),

    'settings' => [

        'default' => [

            'HTML.Doctype' => 'HTML 4.01 Transitional',

            'HTML.Allowed' =>
            'p,br,strong,b,em,i,u,s,blockquote,' .
                'ul,ol,li,h1,h2,h3,h4,' .
                'a[href|title|target],' .
                'table,thead,tbody,tfoot,tr,th,td',

            'CSS.AllowedProperties' => [
                'color',
                'background-color',
                'text-align',
                'font-weight',
                'font-style',
                'text-decoration',
            ],

            'Attr.AllowedFrameTargets' => [
                '_blank',
            ],

            'AutoFormat.AutoParagraph' => true,

            'AutoFormat.RemoveEmpty' => true,
        ],
    ],
];

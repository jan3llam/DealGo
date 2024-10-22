<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'codes' => [
        'success' => [
            'code' => "1",
            'message' => 'Success'
        ],
        'alreadyExist' => [
            'code' => "-11",
            'message' => 'Username, gsm or email already exist'
        ],
        'alreadyExistEmail' => [
            'code' => "-11",
            'message' => 'Email already exist'
        ],
        'alreadyExistPhone' => [
            'code' => "-11",
            'message' => 'Phone already exist'
        ],
        'missingParameters' => [
            'code' => "-12",
            'message' => 'Missing required parameters'
        ],
        'userNotFound' => [
            'code' => "-13",
            'message' => 'User not found'
        ],
        'codeNotValid' => [
            'code' => "-14",
            'message' => 'Code not valid'
        ],
        'accountNotActive' => [
            'code' => "-15",
            'message' => 'Account not active'
        ],
        'accountNotVerified' => [
            'code' => "-30",
            'message' => 'Account not verified'
        ],
        'objectNotFound' => [
            'code' => "-16",
            'message' => 'Object not found'
        ],
        'operationNotPermitted' => [
            'code' => "-17",
            'message' => 'Operation not permitted'
        ],
        'profileNotCompleted' => [
            'code' => "-18",
            'message' => 'Profile not completed'
        ],
        'paymentNotCompleted' => [
            'code' => "-19",
            'message' => 'Payment Not Completed'
        ],
        'gsmNotMatch' => [
            'code' => "-20",
            'message' => 'GSM must be in Saudi Arabia'
        ],
        'relationAlreadyExist' => [
            'code' => "-21",
            'message' => 'Relation is already exists'
        ],
        'channelError' => [
            'code' => "-22",
            'message' => 'Error creating channel'
        ],
        'alreadyRated' => [
            'code' => "-23",
            'message' => 'You already rated this item'
        ],
        'privateAccount' => [
            'code' => "-24",
            'message' => 'This account is private'
        ],
        'fileNotValid' => [
            'code' => "-25",
            'message' => 'File not valid'
        ],
        'alreadyExistVessel' => [
            'code' => "-28",
            'message' => 'Vessel already exist'
        ],
        'notAuthorized' => [
            'code' => "-26",
            'message' => 'You\'re not authorized to do this action'
        ],
        'wrongUsernamePwd' => [
            'code' => "-27",
            'message' => 'Wrong username / password'
        ],
        'cannotDelete' => [
            'code' => "-28",
            'message' => 'Relations found, item cannot be deleted'
        ],
        //HTTP Errors
        'unauthorized' => [
            'code' => "-401",
            'message' => 'Unauthorized'
        ],
        'methodNotAllowed' => [
            'code' => "-405",
            'message' => 'Method Not Allowed'
        ],
        'badRequest' => [
            'code' => "-400",
            'message' => 'Bad request'
        ],
        'forbidden' => [
            'code' => "-403",
            'message' => 'Forbidden'
        ],
        'resourceNotFound' => [
            'code' => "-404",
            'message' => 'Resource Not Found'
        ],
        'objectCreated' => [
            'code' => "-201",
            'message' => 'Object Created'
        ],
        'noContent' => [
            'code' => "-204",
            'message' => 'No Content'
        ],
        'partialContent' => [
            'code' => "-206",
            'message' => 'Partial Content'
        ]
    ],

    'sms' => [
        'welcomeText' => 'Thank you for using Aljalad app, your verification code is :code, it\'s valid for :valid minutes',
        'inviteText' => 'Your friend :name is using Aljalad app, and invites you to try it, check it out at https://www.aljalad.com',
    ],

    'email' => [
        'welcomeText' => 'Hi there,',
        'inviteText' => 'Kindly use (:password) as your verification code to reset your password',
        'passwordText' => 'Kindly use (:password) as your password to login to your account',
        'activationText' => 'Kindly use (:password) as your activation code',
    ]
];

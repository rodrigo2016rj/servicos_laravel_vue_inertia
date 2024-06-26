<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
   */

  'imgur' => [
    'client_id' => env('IMGUR_CLIENT_ID'),
    'client_secret' => env('IMGUR_CLIENT_SECRET'),
    'access_token' => env('IMGUR_ACCESS_TOKEN'),
    'token_type' => env('IMGUR_TOKEN_TYPE'),
    'refresh_token' => env('IMGUR_REFRESH_TOKEN')
  ],
  'facebook' => [
    'id_do_app' => env('FACEBOOK_ID_DO_APP'),
    'versao_da_api' => env('FACEBOOK_VERSAO_DA_API')
  ],
  'postmark' => [
    'token' => env('POSTMARK_TOKEN')
  ],
  'ses' => [
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1')
  ],
  'slack' => [
    'notifications' => [
      'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
      'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL')
    ]
  ]
];

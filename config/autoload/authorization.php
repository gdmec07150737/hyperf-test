<?php

declare(strict_types=1);

return [
    'private_key' => env('AUTH_PRIVATE_KEY', '/mnt/e/code/php-code/private-new.key'),
    'public_key' => env('AUTH_PUBLIC_KEY', '/mnt/e/code/php-code/public-new.key'),
    //加密密钥字符串，通过 base64_encode(random_bytes(32)) 产生。注意如果随意改变它会导致验证和刷新 token 有问题
    'encryption_key' => env('AUTH_ENCRYPTION_KEY', 'T2x2+1OGrElaminasS+01OUmwhOcJiGmE58UD1fllNn6CGcQ='),
    'client_credentials_grant' => [
        //示例： P2Y4DT6H8M 代表两年四天六小时八分钟，规则是以字母P开头，日期放在P后面，如果有时间则接着字母T
        'access_token_ttl' => env('ACCESS_TOKEN_EXPIRATION_TIME', 'PT1H')
    ],
    'password_grant' => [
        'access_token_ttl' => env('ACCESS_TOKEN_EXPIRATION_TIME', 'PT1H')
    ],
    'refresh_token_grant' => [
        'refresh_token_ttl' => env('REFRESH_TOKEN_EXPIRATION_TIME', 'P1M'),
        'access_token_ttl' => env('ACCESS_TOKEN_EXPIRATION_TIME', 'PT1H')
    ],
    'implicit_grant' => [
        'implicit_grant_ttl' => env('IMPLICIT_GRANT_EXPIRATION_TIME', 'PT1H'),
        'access_token_ttl' => env('ACCESS_TOKEN_EXPIRATION_TIME', 'PT1H')
    ],
    'auth_code_grant' => [
        'auth_code_grant_ttl' => env('AUTH_CODE_GRANT_EXPIRATION_TIME', 'PT10M'),
        'refresh_token_ttl' => env('REFRESH_TOKEN_EXPIRATION_TIME', 'P1M'),
        'access_token_ttl' => env('ACCESS_TOKEN_EXPIRATION_TIME', 'PT1H')
    ],
];
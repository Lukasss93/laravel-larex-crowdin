<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Crowdin API Token
     |--------------------------------------------------------------------------
     |
     | Enter your Crowdin Personal Access Token here.
     | You can generate your token at this url: https://crowdin.com/settings#api-key
     |
     | Please note: this library supports only the Crowdin API v2
     |
     */

    'token' => env('LAREX_CROWDIN_TOKEN'),

    /*
     |--------------------------------------------------------------------------
     | Crowdin Project Id
     |--------------------------------------------------------------------------
     |
     | Enter your Crowdin Project Id here.
     | You can get your project id (API v2) at this url: https://crowdin.com/project/your-project/settings#api
     |
     */

    'project_id' => (int)env('LAREX_CROWDIN_PROJECT_ID'),

    /*
     |--------------------------------------------------------------------------
     | Crowdin Organization Name
     |--------------------------------------------------------------------------
     |
     | Optional.
     | Enter your Crowdin Organization Name here.
     |
     */

    'organization' => env('LAREX_CROWDIN_ORGANIZATION'),
];

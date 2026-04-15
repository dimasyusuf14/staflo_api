<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Service Account Credentials Path
    |--------------------------------------------------------------------------
    |
    | Path to your Firebase service account JSON file.
    | Download it from: Firebase Console → Project Settings → Service Accounts
    |                   → Generate new private key
    |
    | Store the file in storage/app/firebase/ and set the path in .env:
    |   FIREBASE_CREDENTIALS=storage/app/firebase/service-account.json
    |
    */
    'credentials_path' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account.json')),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase project ID. This is usually auto-read from the credentials
    | file, but you can override it here.
    |
    */
    'project_id' => env('FIREBASE_PROJECT_ID', null),
];

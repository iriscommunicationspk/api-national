# Office 365 Authentication Setup

## Required Environment Variables

Add these variables to your `.env` file:

```
# Microsoft Office 365 OAuth Settings
MICROSOFT_CLIENT_ID=your-client-id-here
MICROSOFT_CLIENT_SECRET=your-client-secret-here
MICROSOFT_REDIRECT_URI=http://localhost:8000/api/auth/office365/callback
MICROSOFT_TENANT_ID=common
FRONTEND_URL=http://localhost:5173
```

## Steps to Register Your App with Microsoft Azure

1. Go to the [Azure Portal](https://portal.azure.com/)
2. Navigate to "Azure Active Directory" > "App registrations" > "New registration"
3. Enter a name for your application
4. Set the supported account types (typically "Accounts in any organizational directory")
5. Add a redirect URI: `http://localhost:8000/api/auth/office365/callback` (or your production URL)
6. Click "Register"
7. On the app overview page, copy the "Application (client) ID" to your MICROSOFT_CLIENT_ID
8. Go to "Certificates & secrets" > "New client secret"
9. Create a new secret and copy its value to your MICROSOFT_CLIENT_SECRET
10. Go to "API permissions" and add the following permissions:
    - Microsoft Graph > User.Read
    - Microsoft Graph > email
    - Microsoft Graph > profile
    - Microsoft Graph > offline_access
11. Click "Grant admin consent"

## Troubleshooting

If you encounter errors like "Driver [microsoft] not supported", make sure:

1. You have installed the required packages:

    ```
    composer require laravel/socialite
    composer require socialiteproviders/microsoft-azure
    ```

2. You have registered the service providers in `config/app.php`:

    ```php
    'providers' => [
        // ...
        Laravel\Socialite\SocialiteServiceProvider::class,
        App\Providers\MicrosoftServiceProvider::class,
    ],

    'aliases' => [
        // ...
        'Socialite' => Laravel\Socialite\Facades\Socialite::class,
    ],
    ```

3. You have created the Microsoft service provider at `app/Providers/MicrosoftServiceProvider.php`:

    ```php
    <?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;
    use SocialiteProviders\Manager\SocialiteWasCalled;

    class MicrosoftServiceProvider extends ServiceProvider
    {
        public function boot(): void
        {
            $this->app->make('events')->listen(SocialiteWasCalled::class, 'SocialiteProviders\\Azure\\AzureExtendSocialite@handle');
        }
    }
    ```

4. You have properly configured `config/services.php`:

    ```php
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect' => env('MICROSOFT_REDIRECT_URI', '/api/auth/office365/callback'),
        'tenant' => env('MICROSOFT_TENANT_ID', 'common'),
    ],
    ```

5. Remember to clear your config cache after making changes:
    ```
    php artisan config:clear
    ```

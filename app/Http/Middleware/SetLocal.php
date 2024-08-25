<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get the preferred language from the browser
        $preferredLanguage = $request->getPreferredLanguage(Config::get('app.locales'));

        // Set the application's locale
        App::setLocale($preferredLanguage);

        return $next($request);
    }
}

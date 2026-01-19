<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Router\Route;
use Joomla\CMS\User\User;
use YOOtheme\Config;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use function YOOtheme\app;

class CheckUserPermission
{
    public User $user;
    public Config $config;

    public static array $themeRoutes = [
        '/builder/template' => ['GET', 'POST', 'DELETE'],
        '/builder/template/reorder' => ['POST'],
        '/cache' => ['GET'],
        '/cache/clear' => ['POST'],
        '/import' => ['POST'],
        '/styler/library' => ['GET', 'POST', 'DELETE'],
        '/systemcheck' => ['GET'],
        '/theme/style' => ['GET', 'POST'],
        '/theme/styles' => ['GET'],
    ];

    public function __construct(Config $config, User $user)
    {
        $this->user = $user;
        $this->config = $config;
    }

    /**
     * Check permission of current user.
     *
     * @param Request  $request
     * @param callable $next
     */
    public function handle($request, callable $next): Response
    {
        if (!$request->getAttribute('allowed') && !$this->hasPermission($request)) {
            // redirect guest user to user login
            if (
                $this->user->guest &&
                str_contains($request->getHeaderLine('Accept'), 'text/html')
            ) {
                $url = Route::_(
                    $this->config->get('app.isAdmin')
                        ? 'index.php?option=com_login'
                        : 'index.php?option=com_users&view=login',
                    false,
                );

                return app(Response::class)->withRedirect($url);
            }

            $request->abort(403, 'Insufficient User Rights.');
        }

        return $next($request);
    }

    protected function hasPermission($request): bool
    {
        if ($this->user->authorise('core.edit', 'com_templates')) {
            return true;
        }

        $route = $request->getAttribute('route');
        if (in_array($request->getMethod(), static::$themeRoutes[$route->getPath()] ?? [])) {
            return false;
        }

        if (
            $request->getAttribute('customizer') &&
            $request->getQueryParam('section') !== 'builder'
        ) {
            return false;
        }

        return $this->user->authorise('core.edit', 'com_content') ||
            $this->user->authorise('core.edit.own', 'com_content') ||
            $this->user->authorise('core.edit', 'com_modules');
    }
}

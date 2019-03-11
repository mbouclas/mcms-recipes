<?php

namespace Mcms\Recipes\Middleware;

use Carbon\Carbon;
use Closure;
use Mcms\Recipes\Models\Recipe;

/**
 * Look for all recipes about to be published and activate them
 *
 * Class PublishRecipe
 * @package Mcms\Recipes\Middleware
 */
class PublishRecipe
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Recipe::where('published_at', '>=', Carbon::now())->update(['active'=> true]);

        return $next($request);
    }
}
<?php

namespace Mcms\Recipes\Models\Filters;


use App;


use Mcms\Core\QueryFilters\FilterableDate;
use Mcms\Core\QueryFilters\FilterableExtraFields;
use Mcms\Core\QueryFilters\FilterableLimit;
use Mcms\Core\QueryFilters\FilterableOrderBy;
use Mcms\Core\QueryFilters\QueryFilters;
use Mcms\Core\QueryFilters\FilterableTagged;


class RecipeFilters extends QueryFilters
{
    use FilterableDate, FilterableOrderBy, FilterableLimit, FilterableExtraFields, FilterableTagged;

    /**
     * @var array
     */
    protected $filterable = [
        'id',
        'title',
        'slug',
        'description',
        'description_long',
        'userId',
        'active',
        'dateStart',
        'dateEnd',
        'category_id',
        'orderBy',
        'extraFields',
        'q',
        'tag'
    ];

    /**
     * @example ?id=1,0
     * @param null|string $id
     * @return mixed
     */
    public function id($id = null)
    {
        if ( ! isset($id)){
            return $this->builder;
        }


        if (! is_array($id)) {
            $id = $id = explode(',',$id);
        }

        return $this->builder->whereIn('id', $id);
    }


    /**
     * @example ?active=1,0
     * @param null|string $active
     * @return mixed
     */
    public function active($active = null)
    {
        if (is_string($active)) {
            $active = ($active == 'true') ? 1 : false;
        }

        if ( is_null($active)){
            return $this->builder;
        }

        //In case ?active=active,inactive
        if (! is_array($active)) {
            $active = $active = explode(',',$active);
        }

        return $this->builder->whereIn('active', $active);
    }

    /**
     * @example ?userId =1,10
     * @param null|string $user_id
     * @return mixed
     */
    public function userId($user_id = null)
    {
        if ( ! isset($user_id)){
            return $this->builder;
        }

        //In case ?status=user_id,inuser_id
        if (! is_array($user_id)) {
            $user_id = $user_id = explode(',',$user_id);
        }

        return $this->builder->whereIn('user_id', $user_id);
    }

    /**
     * @param null|string $title
     * @return $this
     */
    public function title($title = null)
    {
        $locale = App::getLocale();
        if ( ! $title){
            return $this->builder;
        }

        $title = strtolower($title);
        return $this->builder->whereRaw((\DB::raw("LOWER(`title`->'$.\"{$locale}\"') LIKE '%{$title}%'")));
    }

    public function slug($slug = null)
    {
        if ( ! $slug){
            return $this->builder;
        }

        return $this->builder->where("slug", 'LIKE', "%{$slug}%");
    }

    /**
     * @param null|string $description
     * @return $this
     */
    public function description($description = null)
    {
        $locale = App::getLocale();
        if ( ! $description){
            return $this->builder;
        }

        $description = strtolower($description);
        return $this->builder->whereRaw((\DB::raw("LOWER(`description`->'$.\"{$locale}\"') LIKE '%{$description}%'")));
    }

    /**
     * @param null|string $description_long
     * @return $this
     */
    public function description_long($description_long = null)
    {
        $locale = App::getLocale();
        if ( ! $description_long){
            return $this->builder;
        }

        $description_long = strtolower($description_long);
        return $this->builder->whereRaw((\DB::raw("LOWER(`description_long`->'$.\"{$locale}\"') LIKE '%{$description_long}%'")));

    }

    /**
     * @param null|string $category_id
     * @return $this
     */
    public function category_id($category_id = null)
    {
        if ( ! $category_id){
            return $this->builder;
        }

        if (! is_array($category_id)) {
            $category_id = $category_id = explode(',',$category_id);
        }

        return $this->builder->whereHas('categories', function ($q) use ($category_id){
            $q->whereIn('recipe_category_id', $category_id);
        });
    }


    public function q($q = null)
    {
        if ( ! $q){
            return $this->builder;
        }

        $locale = App::getLocale();

        return $this->builder->where(function($query) use ($q, $locale) {
            $q = strtolower($q);
            $query->orWhereRaw(\DB::raw("LOWER(`title`->'$.\"{$locale}\"') LIKE '%{$q}%'"));
            $query->orWhereRaw(\DB::raw("LOWER(`description`->'$.\"{$locale}\"') LIKE '%{$q}%'"));
            $query->orWhereRaw(\DB::raw("LOWER(`description_long`->'$.\"{$locale}\"') LIKE '%{$q}%'"));
        });
    }


}
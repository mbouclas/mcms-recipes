<?php
/**
 * Created by PhpStorm.
 * User: mbouc
 * Date: 13-Jun-16
 * Time: 12:24 PM
 */

namespace Mcms\Recipes\Services\Recipe;

use Mcms\Recipes\Exceptions\InvalidRecipeFormatException;
use Validator;

class RecipeValidator
{
    public function validate(array $item)
    {
        $check = Validator::make($item, [
            'title' => 'required',
            'user_id' => 'required',
            'active' => 'required',
            'categories' => 'required|array',
        ]);

        if ($check->fails()) {
            throw new InvalidRecipeFormatException($check->errors());
        }

        return true;
    }
}
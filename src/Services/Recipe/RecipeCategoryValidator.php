<?php
/**
 * Created by PhpStorm.
 * User: mbouc
 * Date: 13-Jun-16
 * Time: 12:24 PM
 */

namespace Mcms\Recipes\Services\Recipe;

use Mcms\Recipes\Exceptions\InvalidRecipeCategoryFormatException;
use Validator;

class RecipeCategoryValidator
{
    public function validate(array $item)
    {
        $check = Validator::make($item, [
            'title' => 'required',
            'user_id' => 'required',
            'active' => 'required',
        ]);

        if ($check->fails()) {
            throw new InvalidRecipeCategoryFormatException($check->errors());
        }

        return true;
    }
}
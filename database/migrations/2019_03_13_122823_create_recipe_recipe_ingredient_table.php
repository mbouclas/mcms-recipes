<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipeRecipeIngredientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipe_recipe_ingredient', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('recipe_id')->unsigned()->index();
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->integer('recipe_ingredient_id')->unsigned()->index()->nullable();
            $table->string('title')->nullable();
            $table->smallInteger('unit_id')->nullable();
            $table->string('quantity')->nullable();
            $table->string('type')->nullable();
            $table->boolean('main')->nullable();
            $table->smallInteger('orderBy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipe_recipe_ingredient');
    }
}

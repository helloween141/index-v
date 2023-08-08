<?php

namespace Modules\Archive\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Archive\Entities\base\File;

class FileFactory extends Factory
{
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition()
    {
        return [
        ];
    }
}

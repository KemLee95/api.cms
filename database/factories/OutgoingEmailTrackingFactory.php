<?php

namespace Database\Factories;

use App\Models\OutgoingEmailTracking;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutgoingEmailTrackingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OutgoingEmailTracking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "email" => 'ltkem2103@gmail.com',
            "status" => 'pending'
        ];
    }
}

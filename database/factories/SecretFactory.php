<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SecretFactory extends Factory{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition(){
    $m = rand(0, 8000);

    return [
      'secret' => $this->faker->text(),
      'expires_at' =>($m==0)?null:Carbon::now()->addMinutes($m) ,
      'remainingViews' => rand(1, 50)
    ];
  }
}

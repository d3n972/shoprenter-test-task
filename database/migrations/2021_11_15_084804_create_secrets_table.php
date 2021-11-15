<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecretsTable extends Migration{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(){
    Schema::create('secrets', function(Blueprint $table){
      $table->id();
      $table->timestamp('created_at', 0)->nullable();
      $table->string('hash')->unique();
      $table->text('secret');
      $table->timestamp('expires_at', 0)->nullable();
      $table->integer('remainingViews')->default(0);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(){
    Schema::dropIfExists('secrets');
  }
}

<?php

namespace Tests\Feature;

use Faker\Generator;
use Faker\Provider\Text;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SecretTest extends TestCase{

  /**
   * A basic feature test example.
   *
   * @return void
   */
  public function test_non_existing_secret_are_404(){
    $response = $this->get('/secret/nonexistenthash');

    $response->assertStatus(404);
  }

  private function _createSecretByContentType($accept = 'application/json'){
    $g = new Generator();
    $f = new \Faker\Provider\hu_HU\Text($g);
    return $this->post('/secret', [
      'secret' => $f->realText(),
      'expireAfter' => rand(0, 1000),
      'remainingViews' => rand(1, 50)
    ],
      [
        'Accept' => $accept
      ]);

  }

  public function test_new_secret_responds_with_200(){
    $response = $this->_createSecretByContentType('application/json');
    $response->assertStatus(200);
  }

  public function test_new_secret_responds_with_json_if_json_requested(){
    $response = $this->_createSecretByContentType('application/json');
    $this->key = $response->json()['hash'];
    $response->assertHeader('Content-Type', 'application/json');
  }

  public function test_new_secret_responds_with_xml_if_xml_requested(){
    $response = $this->_createSecretByContentType('application/xml');
    $response->assertHeader('Content-Type', 'application/xml');
  }

  public function test_can_view_unexpired_secret(){
    $response = $this->_createSecretByContentType();
    $response->assertStatus(200);
    $key = $response->json()['hash'];
    $r = $this->get('/secret/' . $key, ['Accept' => 'application/json']);
    $r->assertStatus(200);
    $this->assertTrue($r->json()['hash'] == $key);
  }

  public function test_cant_view_expired_secret_by_viewCount(){
    $g = new Generator();
    $f = new \Faker\Provider\hu_HU\Text($g);
    $k = $this->post('/secret', [
      'secret' => $f->realText(),
      'expireAfter' => rand(0, 1000),
      'remainingViews' => 2
    ],
      [
        'Accept' => 'application/json'
      ]);

    $key = $k->json()['hash'];
    for($i = 0; $i < 2; $i++){
      $r = $this->get('/secret/' . $key, ['accept' => 'application/json']);
      if($i == 0) $r->assertStatus(200);
      if($i == 1) $r->assertStatus(404);
    }
  }

  public function test_cant_view_expired_secret_by_exipiresAt_field(){
    $g = new Generator();
    $f = new \Faker\Provider\hu_HU\Text($g);
    $k = $this->post('/secret', [
      'secret' => $f->realText(),
      'expireAfter' => 1,
      'remainingViews' => 100
    ],
      [
        'Accept' => 'application/json'
      ]);

    $key = $k->json()['hash'];
    $r = $this->get('/secret/' . $key, ['accept' => 'application/json']);
    $r->assertStatus(404);

  }
}

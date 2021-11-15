<?php

namespace App\Http\Controllers;

use App\Models\Secret;
use App\Http\Requests\StoreSecretRequest;
use App\Http\Requests\UpdateSecretRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class SecretController extends Controller{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param \App\Http\Requests\StoreSecretRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreSecretRequest $request){
    $validated = $request->validated();
    $secret = $validated['secret'];
    $remainingViews = $validated['remainingViews'];
    $expires_after = $validated['expireAfter'];

    $m = new Secret();
    $m->hash = hash('sha256', $secret);
    $m->setSecret($secret);
    $m->remainingViews = $remainingViews;
    $m->setExpirationTime($expires_after);
    if($m->save()){
      return response()->preferredFormat($data = $m, $status = 200, $headers = [], $xmlRoot = 'Secret');
    }
    else{
      throw new \Error('1');
    }

  }

  /**
   * Display the specified resource.
   *
   * @param integer $hash
   * @return \Illuminate\Http\Response
   */
  public function show($hash){

    dd(Secret::all());
    $sec = Secret::where('hash', $hash)->first();
    if($sec == null||!$sec->isValid()){
      return response()->preferredFormat($data = [
        'error' => 'Secret not found'
      ], $status = 404, $headers = [], $xmlRoot = 'Secret');
    }


    $sec->saveOrFail();
    return response()->preferredFormat($data = $sec, $status = 200, $headers = [], $xmlRoot = 'Secret');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \App\Http\Requests\UpdateSecretRequest $request
   * @param \App\Models\Secret $secret
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateSecretRequest $request, Secret $secret){
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param \App\Models\Secret $secret
   * @return \Illuminate\Http\Response
   */
  public function destroy(Secret $secret){
    //
  }
}

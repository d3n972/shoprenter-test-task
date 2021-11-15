<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;

/**
 * Code via mtownsend5512/response-xml
 * @link https://github.com/mtownsend5512/response-xml
 */
class RestResponseProvider extends ServiceProvider{
  /**
   * Register services.
   *
   * @return void
   */
  public function register(){
    $this->loadLaravelResponseMacros();
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot(){
    //
  }

  /**
   * If the application is Laravel, load Laravel's response factory and macro the xml methods
   *
   */
  protected function loadLaravelResponseMacros(){
    \Illuminate\Routing\ResponseFactory::macro('xml', function($xml, $status = 200, array $headers = [], $xmlRoot = 'response', $encoding = null){
      if(is_array($xml)){
        $xml = ArrayToXml::convert($xml, $xmlRoot, true, $encoding);
      }
      elseif(is_object($xml) && method_exists($xml, 'toArray')){
        $xml = ArrayToXml::convert($xml->toArray(), $xmlRoot, true, $encoding);
      }
      elseif(is_string($xml)){
        $xml = $xml;
      }
      else{
        $xml = '';
      }
      if(!isset($headers['Content-Type'])){
        $headers = array_merge($headers, ['Content-Type' => 'application/xml']);
      }
      return \Illuminate\Routing\ResponseFactory::make($xml, $status, $headers);
    });

    \Illuminate\Routing\ResponseFactory::macro('preferredFormat', function($data, $status = 200, array $headers = [], $xmlRoot = 'response', $encoding = null){
      $request = Container::getInstance()->make('request');

      /** I needed to disable the dynamic content-type setting because it breaks the signed exchange, see link below
       * @see https://support.google.com/chrome/thread/2381978/err-invalid-signed-exchange?hl=en&msgid=3224823
       */
      if(Str::contains($request->headers->get('Accept'), 'xml')){
        return $this->xml($data, $status, array_merge($headers,[] ), $xmlRoot, $encoding);
      }
      else{
        return $this->json($data, $status,array_merge($headers,[] ), $options = 0);
      }
    });
  }
}

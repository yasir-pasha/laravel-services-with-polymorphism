<?php
  /*
    ==============================
    *   User: Muhammad Yasir
    *   Email: yasir9398@gmail.com
    *   Created By: PhpStorm
    *   Date:   14/10/2020 11:32
    *   Project: laravel-services
    *   File:    ProductFeedService
    ================================
    */
  
  namespace App\Services;
  
  
  use App\Services\ProductFeed\ProductFeed;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\App;

  class ProductFeedService
  {
    /**
     * @description setup and initialize product feed service
     * @param Request $request
     * @return mixed
     * @throws \ErrorException
     */
    public function productFeed(Request $request)
    {
      if(!$request->has('feed_type') && empty($request->get('feed_type'))){
        throw new \ErrorException('Please provide Product Feed type');
      }
      $product_feed_type       = $request->get('feed_type');
      $product_feed_class = "App\Services\ProductFeed\\" . $product_feed_type . '\\' . $product_feed_type . 'ProductFeed';
      if (class_exists($product_feed_class)) {
        $product_feed_class = App::make($product_feed_class);
        $product_feed_setup = new ProductFeed($product_feed_class);
        return $product_feed_setup->generateFeed($request);
      } else {
        throw new \ErrorException(sprintf('There is no product feed class %s found', $product_feed_class));
      }
    }
  }

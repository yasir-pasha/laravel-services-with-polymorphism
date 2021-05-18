<?php
  /*
     ==============================
     *   User: Muhammad Yasir
     *   Email: yasir9398@gmail.com
     *   Created By: PhpStorm
     *   Date:   14/10/2020 11:32
     *   Project: laravel-services
     *   File:    ProductFeedController
     ================================
     */
  
  namespace App\Http\Controllers;
  
  use App\Services\ProductFeedService;
  use Illuminate\Http\Request;
  
  class ProductFeedController extends Controller
  {
    /**
     * @description generate product feed for Facebook/Google
     * @param Request $request
     * @return mixed
     * @throws \ErrorException
     */
    public function generateFeed(Request $request)
    {
      //Initialize product feed service class
      $product_feed = new ProductFeedService();
      return $product_feed->productFeed($request);
    }
  }

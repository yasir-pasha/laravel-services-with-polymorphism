<?php
  
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

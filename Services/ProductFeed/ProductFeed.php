<?php
  /*
    ==============================
    *   User: Muhammad Yasir
    *   Email: yasir9398@gmail.com
    *   Created By: PhpStorm
    *   Date:   14/10/2020 11:32
    *   Project: laravel-services
    *   File:    ProductFeed
    ================================
    */
  
  
  namespace App\Services\ProductFeed;
  
  
  use Illuminate\Http\Request;
  
  class ProductFeed
  {
    /**
     * @var ProductFeedInterface
     */
    private $productFeed;
    
    /**
     * ProductFeed constructor.
     * @param ProductFeedInterface $productFeed
     */
    public function __construct(ProductFeedInterface $productFeed)
    {
      
      $this->productFeed = $productFeed;
    }
  
    /**
     * @description Generate Product feed for Google/Facebook
     * @param Request $request
     * @return mixed
     */
    public function generateFeed(Request $request)
    {
      return $this->productFeed->generateFeed($request);
    }
  }

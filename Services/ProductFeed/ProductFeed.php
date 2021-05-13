<?php
  
  
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

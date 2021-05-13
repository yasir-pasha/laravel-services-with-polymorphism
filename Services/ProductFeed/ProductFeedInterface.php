<?php
  
  
  namespace App\Services\ProductFeed;
  
  
  interface ProductFeedInterface
  {
  
    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function generateFeed(\Illuminate\Http\Request $request);
  }

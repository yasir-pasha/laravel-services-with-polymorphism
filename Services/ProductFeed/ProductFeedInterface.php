<?php
  /*
    ==============================
    *   User: Muhammad Yasir
    *   Email: yasir9398@gmail.com
    *   Created By: PhpStorm
    *   Date:   14/10/2020 11:32
    *   Project: laravel-services
    *   File:    ProductFeedInterface
    ================================
    */
  
  namespace App\Services\ProductFeed;
  
  
  interface ProductFeedInterface
  {
  
    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function generateFeed(\Illuminate\Http\Request $request);
  }

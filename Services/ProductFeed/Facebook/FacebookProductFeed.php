<?php
  /*
    ==============================
    *   User: Muhammad Yasir
    *   Email: yasir9398@gmail.com
    *   Created By: PhpStorm
    *   Date:   14/10/2020 11:32
    *   Project: laravel-services
    *   File:    FacebookProductFeed
    ================================
    */
  
  namespace App\Services\ProductFeed\Facebook;
  
  
  use App\Models\Product;
  use App\Services\ProductFeed\ProductFeedInterface;
  use Carbon\Carbon;
  use Illuminate\Support\Facades\Response;
  use Illuminate\Support\Facades\Storage;
  
  class FacebookProductFeed implements ProductFeedInterface
  {
  
    /**
     * @description Generate product feed for Facebook
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generateFeed(\Illuminate\Http\Request $request)
    {
      ini_set('max_execution_time', -1);
      ini_set('memory_limit', -1);
      $items = [];
      $file_name = 'facebook-catalog.csv';
      $path = 'public/feed/facebook/';
      $file_path = 'public/feed/facebook/'.$file_name;
      if (!Storage::exists($path)) {
        Storage::makeDirectory($path);
      }
      if (Storage::exists($file_path)) {
        Storage::delete($file_path);
      }
      $file    = fopen(storage_path('app/public/feed/facebook/' . $file_name), "w+");
      $headers = ["id", "title", "description", "availability", "condition", "price", "link", "image_link", "brand", "additional_image_link", "age_group", "color", "gender", "item_group_id", "google_product_category","fb_product_category", "material", "pattern", "product_type", "sale_price", "sale_price_effective_date", "shipping", "shipping_weight", "size", "custom_label_0", "custom_label_1", "custom_label_2", "custom_label_3", "custom_label_4", "mpn"];
      fputcsv($file, $headers);
      Product::with('brand:id,name')
        ->with('model:id,name')
        ->with('finish:id,finish')
        ->where('status', 1)
        ->chunk(100, function ($products) use ($file, $file_name) {
          if ($products->count() > 0) {
            
            foreach ($products as $product) {
              
              $item     = [];
              $variants = $product->variants;
              foreach ($variants as $prd) {
                $item['id']           = $prd->id;
                $item['title']        = ucfirst(strtolower($product->product_full_name));
                $item['description']  = $product->meta_description;
                $item['availability'] = 'in stock';
                $item['condition']    = 'new';
                $item['price']        = $prd->uae_retail_price.' AED';
                $item['link']         = env('FRONT_URL','https://www.laravel-services.com').'/' . $product->slug . '/' . $prd->sku;
                $images               = json_decode($product->images);
                $item['image_link']   = url('storage/placeholder.png');
                if (!is_null($images) && count($images) > 0) {
                  $item['image_link'] =  $images[0];
                }
                $item['brand'] = '';
                if (!is_null($product->brand)) {
                  $item['brand'] = optional($product->brand)->name;
                  
                }
                $item['additional_image_link'] = '';
                if (!is_null($images) && count($images) >= 2) {
                  $item['additional_image_link'] = $images[1];
                }
                $item['age_group']               = '';
                $item['color']                   = optional($product->finish)->finish;
                $item['gender']                  = '';
                $item['item_group_id']           = $product->id;
                $item['google_product_category'] = 'Vehicles & Parts > Vehicle Parts & Accessories > Motor Vehicle Parts > Motor Vehicle Wheel Systems > Motor Vehicle Rims & Wheels';
                $item['fb_product_category'] = 'auto > parts & accessories > car parts & accessories';
                $item['material']                = '';
                $item['pattern']                 = $prd->bot_pattern;
                $item['product_type']            = '';
                $item['sale_price']              = '';
                if (!is_null($prd->sale_price)) {
                  $item['sale_price'] = $prd->sale_price .' AED';
                }
                
                $item['sale_price_effective_date'] = '';
                $item['shipping']                  = '';
                $item['shipping_weight']           = '';
                $item['size']                      = $prd->size;
                $item['custom_label_0']            = '';
                $item['custom_label_1']            = '';
                $item['custom_label_2']            = '';
                $item['custom_label_3']            = '';
                $item['custom_label_4']            = '';
                $item['mpn']                       = $prd->sku;
                
                
                fputcsv($file, $item);
              }
            }
          }
        });
      //if cron job than save file otherwise return file for download
      if (!\Illuminate\Support\Facades\Request::has('cron')) {
        $headers = [
          'Cache-Control'         => 'must-revalidate, post-check=0, pre-check=0'
          , 'Content-type'        => 'text/csv'
          , 'Content-Disposition' => 'attachment; filename=' . $file_name
          , 'Expires'             => '0'
          , 'Pragma'              => 'public'
        ];
        return \response()->download(storage_path('app/public/feed/facebook/' . $file_name), $file_name, $headers);
      }
    }
  }

<?php
  /*
    ==============================
    *   User: Muhammad Yasir
    *   Email: yasir9398@gmail.com
    *   Created By: PhpStorm
    *   Date:   14/10/2020 11:32
    *   Project: laravel-services
    *   File:    GoogleProductFeed
    ================================
    */
  
  namespace App\Services\ProductFeed\Google;
  
  
  use App\Models\Product;
  use App\Services\ProductFeed\ProductFeedInterface;
  use Carbon\Carbon;
  use Illuminate\Http\Request;
  use Illuminate\Http\Response;
  use Illuminate\Support\Facades\Storage;
  use LukeSnowden\GoogleShoppingFeed\Containers\GoogleShopping;
  use LukeSnowden\GoogleShoppingFeed\Item;
  
  class GoogleProductFeed implements ProductFeedInterface
  {
    
    /**
     * @description Generate product feed for Google
     * @param Request $request
     * @return Response
     */
    public function generateFeed(Request $request)
    {
      ini_set('max_execution_time', -1);
      ini_set('memory_limit', -1);
      GoogleShopping::title('laravel-services Product feed');
      GoogleShopping::link(url('/'));
      GoogleShopping::description('Our Google Shopping Feed');
      GoogleShopping::setIso4217CountryCode('AED');
      
      Product::with('brand:id,name')
        ->with('model:id,name')
        ->with('finish:id,finish')
        ->where('status', 1)
        ->chunk(100, function ($products) {
          foreach ($products as $product) {
            try {
              $item = GoogleShopping::createItem();
              $item->id($product->id);
              $item->description($product->meta_description);
              $item->title($product->product_full_name);
              
              $item->availability(Item::INSTOCK);
              $item->condition(Item::BRANDNEW);
              
              
              $images = json_decode($product->images);
              try {
                if (!is_null($images) && count($images) > 0) {
                  $item->image_link(htmlspecialchars($images[0]));
                  $item->additional_image_link($images);
                }
              } catch (\Exception $e) {
                $e->getMessage();
              }
              $item->mpn($product->id);
              
              $variants = $product->variants;
              foreach ($variants as $prd) {
                /** create a variant */
                $item->link(env('FRONT_URL', 'https://www.laravel-services.com') . '/' . $product->slug . '/' . $prd->sku);
                $variant = $item->variant();
                $variant->price($prd->uae_retail_price);
                $variant->mpn($prd->sku);
                
                $variant->size($prd->size);
                $variant->customWithNamespace('bolt_pattern', $prd->bolt_pattern);
                if (!is_null($product->model)) {
                  $variant->customWithNamespace('model', $product->model->name);
                }
                if (!is_null($product->finish)) {
                  $variant->customWithNamespace('finish', $product->finish->finish);
                };
                if (!is_null($prd->sale_price)) {
                  $item->sale_price($prd->sale_price);
                }
                
              }
              
              $item->delete();
              /**
               * One thing to note, if creating variants, delete the initial object after you've done,
               * Google no longer needs it!
               *
               * $item->delete();
               *
               */
            } catch (\Exception $e) {
              $e->getMessage();
            }
          }
        });
      
      // boolean value indicates output to browser
      $data = GoogleShopping::asRss();
      $path = 'public/feed/google/';
      if (!Storage::exists('public/feed/facebook/')) {
        Storage::makeDirectory($path);
      }
      $file_name = 'google-feed.xml';
      if (Storage::exists($path . $file_name)) {
        Storage::delete($path . $file_name);
      }
      // $file    = fopen(storage_path($path . $file_name), "w+");
      Storage::put($path . $file_name, $data);
      
      //If not cron job than save file else return file to browser
      if (!\Illuminate\Support\Facades\Request::has('cron')) {
        $response = new Response($data, 200);
        $response->header('Content-Type', 'text/xml');
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . $file_name);
        $response->header('Content-Transfer-Encoding', 'binary');
        return $response;
      }
    }
  }

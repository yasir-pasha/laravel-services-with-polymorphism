<?php
  /**
   * Created by   : yasir
   * Project Name : tunerstop
   * Product Name : PhpStorm
   * Date         : 10/11/20 3:01 PM
   * File Name    : EmailService.php
   */
  
  namespace App\Services;
  
  
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Config;
  use Illuminate\Support\Facades\Log;

  class EmailService
  {
    public function send($to, $title, $body)
    {
      try {
        $cc = 'info@tunerstop.com,dharmin@tunerstop.com';
        $from    = \config('mail.mailers.smtp.username');
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: TUNERSTOP <' . $from . '>' . "\r\n";
        //$headers .= 'cc: <'.$cc.'>' . "\r\n";
  
        if (mail($to, $title, $body, $headers)) {
          // echo 'success';
        } else {
          $error = error_get_last();
          Log::error('Email Error:'.error_get_last()['message']);
         return $errorMessage = error_get_last()['message'];
          //  echo '<pre>'; print_r( error_get_last()); die;
        };
        
      } catch (\Exception $exception) {
        return [
          'message'    => $exception->getMessage(),
          'alert-type' => 'error'
        ];
      }
      
      // die();
    }
  }

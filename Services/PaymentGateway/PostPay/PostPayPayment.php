<?php
  /*
  ==============================
  *   User: Muhammad Yasir
   *  Email:yasir9398@gmail.com
  *   Created By: PhpStorm
  *   Date:   14/04/2021 11:33
  *   Project: laravel-services  
  *   File:    PostPayPayment.php
  ================================
  */
  
  
  namespace App\Services\PaymentGateway\PostPay;
  
  
  use App\Services\PaymentGateway\BasePaymentGateway;
  use Postpay;
  
  class PostPayPayment extends Postpay\Postpay
  {
   
  
    public function __construct()
    {
      parent::__construct([
        'merchant_id' => env('POSTPAY_MERCHANT_ID'),
        'secret_key' => env('POSTPAY_SECRET_ID'),
        'sandbox' => env('POSTAPY_SANDBOX'),
      ]);
    }
    
  }

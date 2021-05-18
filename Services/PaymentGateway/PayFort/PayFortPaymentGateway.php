<?php
  /*
      ==============================
      *   User: Muhammad Yasir
      *   Email: yasir9398@gmail.com
      *   Created By: PhpStorm
      *   Date:   14/10/2020 11:32
      *   Project: laravel-services
      *   File:    PayFortPaymentGateway
      ================================
      */
  

  namespace App\Services\PaymentGateway\PayFort;

  use App\Models\Cart;
  use App\Services\PaymentGateway\{BasePaymentGateway, PaymentGatewayInterface};
  use Illuminate\Http\Request;

  class PayFortPaymentGateway extends PayFort implements PaymentGatewayInterface
  {
    private $gateway = 'PayFort';

    public function initialize(Request $request)
    {
      $this->setMerchantIdentifier(env('PAYFORT_MERCHANT_IDENTIFIER'));
      $this->setAccessCode(env('PAYFORT_ACCESS_CODE'));
      $this->setSHARequestPhrase(env('PAYFORT_SHA_REQUEST_PHRASE'));
      $this->setSHAResponsePhrase(env('PAYFORT_SHA_RESPONSE_PHRASE'));
      $this->setPayment($request);

    }

    public function setPayment(Request $request)
    {
      $this->setAmount($request->get('amount'));
      $this->setCurrency($request->get('currency', 'AED'));
      $this->setItemName($request->get('product_name'));
      $this->setCustomerEmail($request->get('customer_email'));
      $this->cart_items = $request->get('cart_items');

    }
  
    /**
     * @param Request $request
     * @return false|mixed|string
     */
    public function makePayment(Request $request)
    {
      return $data = $this->processRequest($request->get('payment_method'));
    }

    public function setGateway($gateway): void
    {
      $this->gateway = $gateway;

    }
  
    public function confirmOrder(Request $request)
    {
      // TODO: Implement confirmOrder() method.
    }
  
    public function setCart(Cart $cart)
    {
      // TODO: Implement setCart() method.
    }
  
    public function getCart()
    {
      // TODO: Implement getCart() method.
    }
  }

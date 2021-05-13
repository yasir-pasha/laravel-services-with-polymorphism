<?php
  /*
    ==============================
    *   User: Muhammad Yasir
    *   Created By: PhpStorm
    *   Date:   14/10/2020 11:32
    *   Project: tunerstop
    *   File:    PaymentGatewayService
    ================================
    */

  namespace App\Services;


  use App\Services\PaymentGateway\PaymentGateway;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\App;
  use Illuminate\Support\Facades\Config;

  class PaymentGatewayService
  {
    private $cart;
    private $gateway; //PostPay/PayFort
  
    /**
     * PaymentGatewayService constructor.
     * @param string $gateway
     */
    public function __construct(string $gateway)
    {
      $this->gateway = $gateway;
    }
  
    /**
     * @description setup and initialize payment gateway.
     * @param Request $request
     * @return PaymentGateway
     * @throws \ErrorException
     */
    public function initialize(Request $request)
    {
      if(empty($this->gateway)){
        throw new \ErrorException('Please provide payment gateway');
      }
      $payment_gateway       = $this->gateway;
      //Path where the payment gateway's classes exist
      $payment_gateway_class = "App\Services\PaymentGateway\\" . $payment_gateway . '\\' . $payment_gateway . 'PaymentGateway';
      if (class_exists($payment_gateway_class)) {
        $payment_gateway_class = App::make($payment_gateway_class);
        $payment_gateway_class->initialize($request,$payment_gateway);
        return new PaymentGateway($payment_gateway_class);
      } else {
        // Throw exception if provided gateway not found.
        throw new \ErrorException(sprintf('There is no payment gateway %s found', $payment_gateway));
      }
    }
    
  
    /**
     * @return mixed
     */
    public function getCart()
    {
      return $this->cart;
    }
  
    /**
     * @param mixed $cart
     */
    public function setCart($cart): void
    {
      $this->cart = $cart;
    }
  
  }

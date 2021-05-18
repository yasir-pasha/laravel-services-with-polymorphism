<?php
  /**
   * Created by   : yasir
   * Project Name : laravel-services
   * Product Name : PhpStorm
   * Date         : 7/28/20 5:49 PM
   * File Name    : PaymentGateway.php
   */
  
  namespace App\Services\PaymentGateway;
  
  
  use Illuminate\Http\Request;
  
  class PaymentGateway
  {
    private $cart;
    /**
     * @var PaymentGatewayInterface
     */
    private $paymentGateway;
    
    /**
     * PaymentGateway constructor.
     * @param PaymentGatewayInterface $paymentGateway
     */
    public function __construct(PaymentGatewayInterface $paymentGateway)
    {
      
      $this->paymentGateway = $paymentGateway;
    }
  
    /**
     * @param Request $request
     * @return mixed
     */
    public function makePayment(Request $request)
    {
      $this->paymentGateway->setCart($this->getCart());
      return $this->paymentGateway->makePayment($request);
    }
  
    /**
     * @param Request $request
     * @return mixed
     */
    public function confirmOrder(Request $request)
    {
      return $this->paymentGateway->confirmOrder($request);
    }
  
    /**
     * @param $cart
     */
    public function setCart($cart)
    {
      $this->cart = $cart;
    }
  
    /**
     * @return mixed
     */
    public function getCart()
    {
      return $this->cart;
    }
    
  }

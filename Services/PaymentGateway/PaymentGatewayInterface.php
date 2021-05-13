<?php
  
  
  namespace App\Services\PaymentGateway;
  
  
  use App\Models\Cart;
  use Illuminate\Http\Request;
  
  interface PaymentGatewayInterface
  {
    
    /**
     * @description Setup payment gateway
     * @param Request $request
     * @return mixed
     */
    public function initialize(Request $request);
  
    /**
     * @description Make payment to specific payment gateway
     * @param Request $request
     * @return mixed
     */
    public function makePayment(Request $request);
  
    /**
     * @description Confirm the order and update order status after payment
     * @param Request $request
     * @return mixed
     */
    public function confirmOrder(Request $request);
  
    /**
     * @param Cart $cart
     * @return mixed
     */
    public function setCart(Cart $cart);
  
    /**
     * @return mixed
     */
    public function getCart();
  }

<?php
  /**
   * Created by   : yasir
   * Project Name : laravel-services
   * Product Name : PhpStorm
   * Date         : 7/28/20 6:01 PM
   * File Name    : PaypalPaymentGateway.php
   */
  
  namespace App\Services\PaymentGateway\Paypal;
  
  
  use App\Models\Cart;
  use App\Services\PaymentGateway\BasePaymentGateway;
  use App\Services\PaymentGateway\PaymentGatewayInterface;
  use Illuminate\Http\Request;
  
  class PaypalPaymentGateway extends BasePaymentGateway implements PaymentGatewayInterface
  {
    public function setup(Request $request)
    {
      // TODO: Implement setup() method.
    }
  
    public function makePayment(Request $request)
    {
      // TODO: Implement makePayment() method.
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

<?php
  /*
  ==============================
  *   User: Muhammad Yasir
  *   Created By: PhpStorm
  *   Date:   14/04/2021 11:32
  *   Project: laravel-services  
  *   File:    PayPostPaymentGateway.php
  ================================
  */
  
  
  namespace App\Services\PaymentGateway\PostPay;
  
  
  use App\Exceptions\APIErrorException;
  use App\Http\Controllers\Api\OrderController;
  use App\Models\Cart;
  use App\Models\Order;
  use App\Repositories\OrderRepository;
  use App\Services\PaymentGateway\PaymentGatewayInterface;
  use App\Utility\Helper;
  use Carbon\Carbon;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\App;
  use Illuminate\Support\Str;
  use Postpay\Exceptions\RESTfulException;
  
  class PostPayPaymentGateway extends PostPayPayment implements PaymentGatewayInterface
  {
    
    private $cart;
    
    public function initialize(Request $request)
    {
    
    }
    
    /**
     * @description make a payment.
     * @param Request $request
     * @return array
     * @throws \Postpay\Exceptions\PostpayException
     */
    public function makePayment(Request $request)
    {
      $cart         = $this->getCart();
      $total_amount = Helper::calculateTotalAmount($cart) * 100;
      $cart_order   = $cart->cartOrder;
      
      
      $data = [
        'order_id'        => $cart_order->order_number . '-' . Str::random(5),
        'total_amount'    => $total_amount,
        'tax_amount'      => $cart->vat,
        'currency'        => 'AED',
        'shipping'        => $this->getShippingAddress($cart_order),
        'billing_address' => $this->getBillingAddress($cart_order),
        'customer'        => $this->getCustomer($cart_order),
        'items'           => $this->formatItems($cart),
        'discounts'       => [
          [
            'code'   => 'discount-' . Str::random(5),
            'name'   => '',
            'amount' => is_null($cart->discount) ? 0 : $cart->discount
          ]
        ],
        'merchant'        => [
          'confirmation_url' => url('order/confirm-order/'),
          'cancel_url'       => url('cancel-order/')
        ],
        'num_instalments' => 2,
        
        'session_id'   => $cart->session_id,
        'order_number' => $cart->order_number
      ];
      
      
      // $data = json_decode($data, true);
      $data = $this->post('checkouts', $data);
      return $this->getLastResponse()->json();
    }
    
    private function generateOrderId()
    {
      return Str::random(8);
      
    }
    
    public function setCart(Cart $cart)
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
    
    /**
     * @description get shipping address for order
     * @param Order $order
     * @return array
     */
    private function getShippingAddress(Order $order)
    {
      $billing = $order->billing;
      if ($billing->is_same_shipping) {
        $shipping = $billing;
      } else {
        $shipping = $order->shipping;
      }
      
      
      return [
        'id'      => 'shipping-' . Str::random(5),
        'name'    => 'N/A',
        'amount'  => $order->shipping * 100,
        'address' => [
          'first_name'  => $shipping->first_name,
          'last_name'   => $shipping->last_name,
          'phone'       => $shipping->phone,
          'alt_phone'   => '',
          'line1'       => $shipping->address,
          'line2'       => '',
          'city'        => $shipping->city,
          'state'       => '',
          'country'     => 'AE',
          'postal_code' => '00000']
      ];
      
    }
    
    /**
     * @description Get billing address for order
     * @param $order
     * @return array
     */
    private function getBillingAddress($order)
    {
      
      $billing = $order->billing;
      return [
        
        'first_name'  => $billing->first_name,
        'last_name'   => $billing->last_name,
        'phone'       => $billing->phone,
        'alt_phone'   => '',
        'line1'       => $billing->address,
        'line2'       => '',
        'city'        => $billing->city,
        'state'       => '',
        'country'     => 'AE',
        'postal_code' => '00000'
      
      ];
      
    }
    
    /**
     * @description Get customer of order
     * @param $order
     * @return array
     */
    private function getCustomer($order)
    {
      $billing = $order->billing;
      return [
        "id"          => "customer-" . Str::random(5),
        "email"       => $billing->email,
        'first_name'  => $billing->first_name,
        'last_name'   => $billing->last_name,
        "gender"      => "",
        "account"     => "guest",
        // "date_of_birth" => "",
        "date_joined" => Carbon::now()->toIso8601String()
      ];
    }
    
    /**
     *
     * @description format order items for postpay
     * @param Cart $cart
     * @return array
     */
    private function formatItems(Cart $cart)
    {
      $order_items = [];
      $items       = $cart->cartItems;
      if (!empty($items)) {
        foreach ($items as $item) {
          $variant       = $item->productVariant;
          $product       = $variant->product;
          $order_item    = [
            'reference'   => $variant->sku,
            'name'        => $product->product_full_name,
            'description' => '',
            'url'         => env('FRONT_URL') . '/' . $product->slug . '/' . $variant->sku,
            'image_url'   => '',
            'unit_price'  => $item->price * 100,
            'qty'         => $item->quantity
          ];
          $order_items[] = $order_item;
        }
      }
      return $order_items;
    }
    
    /**
     * @description Capture the order after successfully paid and then update order status
     * @param Request $request
     * @return array
     * @throws APIErrorException
     * @throws \Postpay\Exceptions\PostpayException
     */
    public function confirmOrder(Request $request)
    {
      try {
        $order_id         = $request->get('order_id');
        //Post request to PostPay gateway.
        $data             = $this->post("/orders/$order_id/capture");
        $data             = $data->json();
        $result           = $this->updateOrderStatus($data);
        $order_controller = App::make(OrderController::class);
        $request->request->add(['order_id' => $result->id]);
        $order_controller->sendEmail($request);
        return ['order_id' => $result->id];
      } catch (RESTfulException $e) {
        throw new APIErrorException($e->getMessage());
      }
      
    }
    
    /**
     * @param array $data
     * @return Order
     * @throws APIErrorException
     */
    private function updateOrderStatus(array $data): Order
    {
      $order_number = explode('-', $data['order_id']);
      $order_number = $order_number[0];
      $orderRepo    = new OrderRepository();
      
      $data = [
        ['key' => 'postpay_id', 'value' => $data['order_id']],
        ['key' => 'payment_gateway', 'value' => 'postpay'],
        ['key' => 'payment_method', 'value' => 'Card'],
        ['key' => 'payment_type', 'value' => 'full'],
        ['key' => 'paid_amount', 'value' => $data['total_amount'] / 100],
        ['key' => 'status', 'value' => 1],
      
      ];
      return $orderRepo->updateOrder(['key' => 'order_number', 'value' => $order_number], $data);
    }
  }

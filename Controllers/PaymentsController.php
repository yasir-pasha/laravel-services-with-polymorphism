<?php
  
  namespace App\Http\Controllers;
  
  use App\Exceptions\APIErrorException;
  use App\Repositories\CartRepository;
  use App\Services\PaymentGateway\PayFort\PayFort;
  use App\Services\PaymentGatewayService;
  use App\Utility\Helper;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Validator;
  use Postpay\Exceptions\RESTfulException;
  
  class PaymentsController extends Controller
  {
    private $cartRepo;
    
    public function __construct(CartRepository $cartRepo)
    {
      $this->cartRepo = $cartRepo;
    }
  
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws APIErrorException
     * @throws \ErrorException
     * @description Make payment to enabled payment gateway. Payment GateWays: PostPay/PayFort.
     *              Add gateway param in request with value PostPay/PayFort
     */
    public function payment(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'session_id' => 'required'
      ]);
      
      if ($validator->fails()) {
        return Helper::errorResponse(__('validation_errors'), $validator->errors()->all());
      }
      try {
        $cart = $this->cartRepo->getCart($request->session_id);
        $request->request->add(['cart' => json_encode($cart)]);
        $request->request->add(['product_name' => 'Wheel Ream']);
        $request->request->add(['amount' => $cart->total]);
        
        $payment_gateway_service = new PaymentGatewayService($request->gateway);
        $payment_gateway         = $payment_gateway_service->initialize($request);
        $payment_gateway->setCart($cart);
        $data = $payment_gateway->makePayment($request);
        return Helper::successReponse('Payment url received', $data);
      } catch (RESTfulException $e) {
        $r = $e->getResponse();
        if ($r->getStatusCode() == 400) {
          $r             = $r->json();
          $error_message = [];
          if (!empty($r['error'])) {
            $errors = $r['error'];
            foreach ($errors['detail'] as $index => $error) {
              foreach ($error as $error_index => $item) {
                $error_message[] = $index . ' - ' . $error_index . ' - ' . $item[0];
              }
            }
          }
          return Helper::errorResponse('Something went wrong', $error_message);
        } else {
          throw new APIErrorException($e->getMessage());
        }
      }
    }
  }

## **How to use Polymorphism in laravel with example**

This project contains Laravel Services examples. There are two services for Payment Gateway(PostPay, PayFort) and Product Feed(Google and Facebook)

### **How to Use**
1. **Payment Gateway**
 
   The Payment Gateway service is used by providing  the`gateway` parameter to `PaymentGatewayService`. The `gateway` parameter value should be `PostPay` or `PayFort`otherwise exception is thrown. Currently, these two payment gateways are implemented **PostPay** and **PayFort**. The service initializes the payment gateway that is provided in the `$request`.

   **File structure**: There is one file associated with each service(`Services/PaymentGatewayService.php` for PaymentGateWay and `ProductFeedService.php` for Product Feed). This service file initializes the main service object(`PaymentGateway`) by provided gateway parameter. There is a folder associated with each payment gateway inside `Services/PaymentGateway`. The Class `Services/PaymentGateway/PaymentGateway.php` is used to process the request by creating the provided gateway object.

   #### **Example**
        $payment_gateway_service = new PaymentGatewayService($request->gateway);
        $payment_gateway         = $payment_gateway_service->initialize($request);
        $payment_gateway->setCart($cart);
        $data = $payment_gateway->makePayment($request);

2. **Product Feed**
    
   The Product feed service is also initialized in the same way as the payment gateway. Currently, `Facebook` and `Google` Product Feed is implemented.


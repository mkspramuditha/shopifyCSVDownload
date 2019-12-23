<?php


namespace AppBundle\Controller;


use AppBundle\Entity\ShopifyOrder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends DefaultController
{

    /**
     * @Route("/loadOrders", name="loadOrders")
     */
    public function loadOrdersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        $orders = $this->getRepository('ShopifyOrder')->findAll();
//        foreach ($orders as $order){
//            $em->remove($order);
//        }
//        $em->flush();


        for($i=19;$i<1000;$i++){
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://2354d58135ab061a6b441b5631a4b2b8:2feae715e670be2696d1f8f61a9a14c8@hillman.myshopify.com/admin/orders.json?created_at_min=2019-05-24T00:00:00+02:00&limit=250&status=any&page='.$i);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch,CURLOPT_ENCODING , "gzip");


            $headers = array();
            $headers[] = 'Accept: */*';
            $headers[] = 'Accept-Encoding: gzip, deflate';
            $headers[] = 'Authorization: Basic MjM1NGQ1ODEzNWFiMDYxYTZiNDQxYjU2MzFhNGIyYjg6MmZlYWU3MTVlNjcwYmUyNjk2ZDFmOGY2MWE5YTE0Yzg=';
            $headers[] = 'Cache-Control: no-cache';
            $headers[] = 'Connection: keep-alive';
            $headers[] = 'Cookie: __cfduid=d98050f7affed6e9574bda5d68c32f7491571664121';
            $headers[] = 'Host: hillman.myshopify.com';
            $headers[] = 'Postman-Token: 38a4469f-c65b-4ac0-bdb4-0a1e911e9326,febb0004-d945-4dca-ba90-172f9631bb59';
            $headers[] = 'User-Agent: PostmanRuntime/7.20.1';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
//    var_dump($result);exit;
            $orders = json_decode($result,true)['orders'];
//            var_dump(count($orders));exit;
            if(count($orders) < 250){
                var_dump($i);
                break;
            }else{
                foreach ($orders as $order){

                    $customer = $order['customer'];

                    if($customer['default_address']['country'] == "Bulgaria"){
                        $orderObj = new ShopifyOrder();
                        $orderObj->setOrderId($order['id']);
                        $orderObj->setCreatedAt($order['created_at']);
                        $orderObj->setUpdatedAt($order['updated_at']);
                        $orderObj->setNumber($order['number']);
                        $orderObj->setCancelledAt($order['cancelled_at']);
                        $orderObj->setFulfillmentStatus($order['fulfillment_status']);
                        $orderObj->setAcceptMarketing($customer['accepts_marketing']);
                        $orderObj->setAmount($order['total_price']);
                        $orderObj->setCustomerId((string)$customer['id']);
                        $orderObj->setFirstname($customer['first_name']);
                        $orderObj->setLastname($customer['last_name']);
                        $orderObj->setOrderCount($customer['orders_count']);
                        $orderObj->setLastOrderId((string)$customer['last_order_id']);
                        $orderObj->setEmail($customer['email']);
                        $orderObj->setPhone($this->getPhoneNumber($customer['phone']));
                        try{
                            $orderObj->setCustomerPhone($this->getPhoneNumber($order['billing_address']['phone']));
                        }catch (\Exception $e){
                            var_dump($e->getMessage());
                        }

                        try{
                            $orderObj->setShippingPhone($this->getPhoneNumber($order['shipping_address']['phone']));
                        }catch (\Exception $e){
                            var_dump($e->getMessage());
                        }

                        try{
                            if (!$em->isOpen()) {
                                $em = $em->create(
                                    $em->getConnection(),
                                    $em->getConfiguration()
                                );
                            }
                            $em->persist($orderObj);
                            $em->flush();
                        }catch (\Exception $e){
                            var_dump($e->getMessage());
                        }
                    }

                }

                var_dump($i);
            }
        }
        exit;

    }

    /**
     * @Route("/orders/update", name="update_orders")
     */
    public function updateOrders(){
        $dates = $this->getRepository('ShopifyOrder')->getLatestUpdateDate();
        $latestUpdatedDate = $dates['latest_updated_date'];
//        var_dump($latestUpdatedDate);
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $this->getRepository('ShopifyOrder');

        for($i=1;$i<1000;$i++){
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://2354d58135ab061a6b441b5631a4b2b8:2feae715e670be2696d1f8f61a9a14c8@hillman.myshopify.com/admin/orders.json?updated_at_min='.$latestUpdatedDate.'&limit=250&status=any&page='.$i);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch,CURLOPT_ENCODING , "gzip");


            $headers = array();
            $headers[] = 'Accept: */*';
            $headers[] = 'Accept-Encoding: gzip, deflate';
            $headers[] = 'Authorization: Basic MjM1NGQ1ODEzNWFiMDYxYTZiNDQxYjU2MzFhNGIyYjg6MmZlYWU3MTVlNjcwYmUyNjk2ZDFmOGY2MWE5YTE0Yzg=';
            $headers[] = 'Cache-Control: no-cache';
            $headers[] = 'Connection: keep-alive';
            $headers[] = 'Cookie: __cfduid=d98050f7affed6e9574bda5d68c32f7491571664121';
            $headers[] = 'Host: hillman.myshopify.com';
            $headers[] = 'Postman-Token: 38a4469f-c65b-4ac0-bdb4-0a1e911e9326,febb0004-d945-4dca-ba90-172f9631bb59';
            $headers[] = 'User-Agent: PostmanRuntime/7.20.1';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
//    var_dump($result);exit;
            $orders = json_decode($result,true)['orders'];
//            var_dump(count($orders));
//            var_dump(count($orders));exit;
            if(count($orders) < 250){
//                var_dump($i);
                break;
            }else{
                foreach ($orders as $order){

                    $customer = $order['customer'];

                    if($customer['default_address']['country'] == "Bulgaria"){
                        $orderObj = $orderRepository->findOneBy(array('orderId'=>$order['id']));
                        if($orderObj == null){
                            $orderObj = new ShopifyOrder();
                        }
                        $orderObj->setOrderId($order['id']);
                        $orderObj->setCreatedAt($order['created_at']);
                        $orderObj->setUpdatedAt($order['updated_at']);
                        $orderObj->setNumber($order['number']);
                        $orderObj->setCancelledAt($order['cancelled_at']);
                        $orderObj->setFulfillmentStatus($order['fulfillment_status']);
                        $orderObj->setAcceptMarketing($customer['accepts_marketing']);
                        $orderObj->setAmount($order['total_price']);
                        $orderObj->setCustomerId((string)$customer['id']);
                        $orderObj->setFirstname($customer['first_name']);
                        $orderObj->setLastname($customer['last_name']);
                        $orderObj->setOrderCount($customer['orders_count']);
                        $orderObj->setLastOrderId((string)$customer['last_order_id']);
                        $orderObj->setEmail($customer['email']);
                        $orderObj->setPhone(str_replace("+","",$customer['phone']));
                        $orderObj->setCustomerPhone(str_replace(["+"," "],"",$order['billing_address']['phone']));
                        $orderObj->setShippingPhone(str_replace(["+"," "],"",$order['shipping_address']['phone']));
                        $em->persist($orderObj);
                    }

                }
                $em->flush();
            }
        }
        return null;


    }

    /**
     * @Route("/orders/generate/csv", name="generate_csv_orders")
     */
    public function generateCSV(){

        $this->updateOrders();

        $orders = $this->getRepository('ShopifyOrder')->findAll();

        $orderMap = array();
        foreach ($orders as $order){
            $orderMap[$order->getOrderId()] = $order->getCreatedAt();
        }


        $csvArray = array();
        $headers = array('STATUS','ORDER NUMBER','LAST ORDER DATE','TTL ORDERS','ACCEPT MARKETING','AMOUNT','FIRST NAME','LAST NAME','EMAIL','PHONE','CUSTOMER PHONE','SHIPPING PHONE');
        $csvArray[] = $headers;
        foreach ($orders as $order){
            $csvLine = array();
            if($order->getCancelledAt() != null){
                $csvLine[] = "Cancelled";
            }else{
                if($order->getFulfillmentStatus()== null){
                    $csvLine[] = "Unfulfilled";
                }else{
                    $csvLine[] = "Fulfilled";
                }
            }

            $csvLine[] = $order->getNumber();
            if(array_key_exists($order->getLastOrderId(),$orderMap)){
                $csvLine[] = $orderMap[$order->getLastOrderId()];
            }else{
                $csvLine[] = "";
            }
            $csvLine[] = (string) $order->getOrderCount();
            $csvLine[] = (string) $order->getAcceptMarketing();
            $csvLine[] = $order->getAmount();
            $csvLine[] = $order->getFirstname();
            $csvLine[] = $order->getLastname();
            $csvLine[] = $order->getEmail();
            $csvLine[] = $this->getPhoneNumber($order->getPhone());
            $csvLine[] = $this->getPhoneNumber($order->getCustomerPhone());
            $csvLine[] = $this->getPhoneNumber($order->getShippingPhone());

            $csvArray[] = $csvLine;
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="orders.csv"');
        $fp = fopen('php://output', 'wb');
        foreach ($csvArray as $line){
            fputcsv($fp, $line);
        }

        fclose($fp);
        exit;
    }

}
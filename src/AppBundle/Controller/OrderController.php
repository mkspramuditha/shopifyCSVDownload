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
        $shopId = $request->get('shop');
        if($shopId == null){
            var_dump("shop id not given");
            exit;
        }
        $shop = $this->getRepository('Shop')->find($shopId);
        if($shop == null){
            var_dump("shop not exists");
            exit;
        }
        $em = $this->getDoctrine()->getManager();
//        $orders = $this->getRepository('ShopifyOrder')->findAll();
//        foreach ($orders as $order){
//            $em->remove($order);
//        }
//        $em->flush();
//        var_dump($shop->getUrl());exit;

        for($i=63;$i<100;$i++){
            $ch = curl_init();
//            var_dump($shop->getUrl().'/admin/orders.json?created_at_min=2019-05-24T00:00:00+02:00&limit=250&status=any&page='.$i);exit;
//            curl_setopt($ch, CURLOPT_URL, "https://3623623bf53c36da004aa47174a0511b:cd7b56023e4c8109ca530baad06f1c36@hillmande.myshopify.com/admin/orders/count.json?created_at_min=2019-05-24T00:00:00+02:00&limit=250&status=any");

            if($shop->getCountrySelect()){
                curl_setopt($ch, CURLOPT_URL, $shop->getUrl().'/admin/orders.json?created_at_min=2019-05-24T00:00:00+02:00&limit=250&status=any&page='.$i);
            }else{
                curl_setopt($ch, CURLOPT_URL, $shop->getUrl().'/admin/orders.json?limit=250&status=any&page='.$i);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch,CURLOPT_ENCODING , "gzip");

            $hostSplitText = explode("@",$shop->getUrl());
            $headers = array();
            $headers[] = 'Accept: */*';
            $headers[] = 'Accept-Encoding: gzip, deflate';
            $headers[] = 'Authorization: Basic '.base64_encode($shop->getAuthorization());
            $headers[] = 'Cache-Control: no-cache';
            $headers[] = 'Connection: keep-alive';
            $headers[] = 'Cookie: __cfduid=d98050f7affed6e9574bda5d68c32f7491571664121';
            $headers[] = 'Host: '.end($hostSplitText);
            $headers[] = 'Postman-Token: 38a4469f-c65b-4ac0-bdb4-0a1e911e9326,febb0004-d945-4dca-ba90-172f9631bb59';
            $headers[] = 'User-Agent: PostmanRuntime/7.20.1';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
//            var_dump($result);exit;
            $orders = json_decode($result,true)['orders'];
//            var_dump(count($orders));exit;
            if(count($orders) == 0){
                var_dump($i);
                var_dump("products over");
                break;
            }else{
                foreach ($orders as $order){
                    if(array_key_exists('customer',$order)){
                        $customer = $order['customer'];
                    }else{
                        continue;
                    }

                    if(!$shop->getCountrySelect() || $customer['default_address']['country'] == "Bulgaria"){
                        $orderObj = new ShopifyOrder();
                        $orderObj->setOrderId($order['id']);
                        $orderObj->setCreatedAt($order['created_at']);
                        $orderObj->setUpdatedAt($order['updated_at']);
                        $orderObj->setNumber($order['order_number']);
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
                        $orderObj->setOrderUrl($order['order_status_url']);
                        $orderObj->setOrderName($order['name']);
                        $orderObj->setTags($order['tags']);
                        if(is_array($order['fulfillments']) && count($order['fulfillments']) > 0){
                            $orderObj->setWaybillId($order['fulfillments'][0]['tracking_number']);
                        }else{
                            $orderObj->setWaybillId("");
                        }
                        if(key_exists('shipping_address',$order)){
                            $orderObj->setShippingCountry($order['shipping_address']['country']);
                        }

                        $mainPhoneNumber = $order['phone'];

                        if($customer['phone'] != null){
                            $mainPhoneNumber = $customer['phone'];
                        }
                        if($shop->getNumberCorrection()){
                            $orderObj->setPhone($this->getPhoneNumber($mainPhoneNumber,"359"));
                        }else{
                            $orderObj->setPhone($mainPhoneNumber);
                        }
                        $orderObj->setShop($shopId);
                        try{
                            if($shop->getNumberCorrection()) {
                                $orderObj->setCustomerPhone($this->getPhoneNumber($order['billing_address']['phone'],"359"));
                            }else{
                                $orderObj->setCustomerPhone($order['billing_address']['phone']);
                            }
                        }catch (\Exception $e){
                            var_dump($e->getMessage());
                        }

                        try{
                            if($shop->getNumberCorrection()) {
                                $orderObj->setShippingPhone($this->getPhoneNumber($order['shipping_address']['phone'],"359"));
                            }else{
                                $orderObj->setShippingPhone($order['shipping_address']['phone']);
                            }
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
    public function updateOrders(Request $request){
        $shopId = $request->get('shop');
        if($shopId == null){
            var_dump("shop id not given");
            exit;
        }
        $shop = $this->getRepository('Shop')->find($shopId);
        if($shop == null){
            var_dump("shop not exists");
            exit;
        }

        $dates = $this->getRepository('ShopifyOrder')->getLatestUpdateDate($shop->getId());
        $latestUpdatedDate = $dates['latest_updated_date'];
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $this->getRepository('ShopifyOrder');
        for($i=1;$i<1000;$i++){
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $shop->getUrl().'/admin/orders.json?updated_at_min='.$latestUpdatedDate.'&limit=250&status=any&page='.$i);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch,CURLOPT_ENCODING , "gzip");

            $hostSplitText = explode("@",$shop->getUrl());
            $headers = array();
            $headers[] = 'Accept: */*';
            $headers[] = 'Accept-Encoding: gzip, deflate';
            $headers[] = 'Authorization: Basic '.base64_encode($shop->getAuthorization());
            $headers[] = 'Cache-Control: no-cache';
            $headers[] = 'Connection: keep-alive';
            $headers[] = 'Cookie: __cfduid=d98050f7affed6e9574bda5d68c32f7491571664121';
            $headers[] = 'Host: '.end($hostSplitText);
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
            if(count($orders) == 0){
//                var_dump($i);
                break;
            }else{
                foreach ($orders as $order){

                    $customer = $order['customer'];

                    if(!$shop->getCountrySelect() || $customer['default_address']['country'] == "Bulgaria"){
                        $orderObj = $orderRepository->findOneBy(array('orderId'=>$order['id']));
                        if($orderObj == null){
                            $orderObj = new ShopifyOrder();
                        }
                        $orderObj->setOrderId($order['id']);
                        $orderObj->setCreatedAt($order['created_at']);
                        $orderObj->setUpdatedAt($order['updated_at']);
                        $orderObj->setNumber($order['order_number']);
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
                        $orderObj->setOrderUrl($order['order_status_url']);
                        $orderObj->setOrderName($order['name']);
                        $orderObj->setTags($order['tags']);
                        if(is_array($order['fulfillments']) && count($order['fulfillments']) > 0){
                            $orderObj->setWaybillId($order['fulfillments'][0]['tracking_number']);
                        }else{
                            $orderObj->setWaybillId("");
                        }
                        if(key_exists('shipping_address',$order)){
                            $orderObj->setShippingCountry($order['shipping_address']['country']);
                        }

                        $mainPhoneNumber = $order['phone'];

                        if($customer['phone'] != null){
                            $mainPhoneNumber = $customer['phone'];
                        }

                        if($shop->getNumberCorrection()){
                            $orderObj->setPhone($this->getPhoneNumber($mainPhoneNumber,"359"));
                        }else{
                            $orderObj->setPhone($mainPhoneNumber);
                        }
                        $orderObj->setShop($shopId);
                        try{
                            if($shop->getNumberCorrection()) {
                                $orderObj->setCustomerPhone($this->getPhoneNumber($order['billing_address']['phone'],"359"));
                            }else{
                                $orderObj->setCustomerPhone($order['billing_address']['phone']);
                            }
                        }catch (\Exception $e){
                            var_dump($e->getMessage());
                        }

                        try{
                            if($shop->getNumberCorrection()) {
                                $orderObj->setShippingPhone($this->getPhoneNumber($order['shipping_address']['phone'],"359"));
                            }else{
                                $orderObj->setShippingPhone($order['shipping_address']['phone']);
                            }
                        }catch (\Exception $e){
                            var_dump($e->getMessage());
                        }
                        $em->persist($orderObj);
                    }

                }
                $em->flush();
            }
        }
//        exit;
        return null;


    }

    /**
     * @Route("/orders/generate/csv", name="generate_csv_orders")
     */
    public function generateCSV(Request $request){


        $shopId = $request->get('shop');
        if($shopId == null){
            var_dump("shop id not given");
            exit;
        }
        $shop = $this->getRepository('Shop')->find($shopId);
        if($shop == null){
            var_dump("shop not exists");
            exit;
        }

        $this->updateOrders($request);

        $orders = $this->getRepository('ShopifyOrder')->findBy(array('shop'=>$shopId));
        $orderMap = array();
        $uniqueOrderMap = array();
        foreach ($orders as $order){
            if(key_exists($order->getCustomerId(),$uniqueOrderMap)){
                if($order->getCreatedAt() > $uniqueOrderMap[$order->getCustomerId()]->getCreatedAt()){
                    $uniqueOrderMap[$order->getCustomerId()] = $order;
                }
            }else{
                $uniqueOrderMap[$order->getCustomerId()] = $order;
            }
            $orderMap[$order->getOrderId()] = $order->getCreatedAt();
        }



        $csvArray = array();
        $headers = array('STATUS','ORDER NUMBER','LAST ORDER DATE','TTL ORDERS','ACCEPT MARKETING','AMOUNT','FIRST NAME','LAST NAME','EMAIL','PHONE','CUSTOMER PHONE','SHIPPING PHONE','SHIPPING COUNTRY');
        $csvArray[] = $headers;


        foreach ($uniqueOrderMap as $order){
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


            $phone = $this->getPhoneNumber($order->getPhone(),$this->getPhonePrefix(strtolower($order->getShippingCountry())));
            $customerPhone =  $this->getPhoneNumber($order->getCustomerPhone(),$this->getPhonePrefix(strtolower($order->getShippingCountry())));
            $shippingPhone = $this->getPhoneNumber($order->getShippingPhone(),$this->getPhonePrefix(strtolower($order->getShippingCountry())));


            $csvLine[] = $phone;
            $csvLine[] =$customerPhone;
            if($shippingPhone == ""){
                if($customerPhone == ""){
                    $csvLine[] = $phone;
                }else{
                    $csvLine[] = $customerPhone;
                }
            }else{
                $csvLine[] = $shippingPhone;
            }

            $csvLine[] = $order->getShippingCountry();

            $csvArray[] = $csvLine;
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$shop->getName().' - orders.csv"');
        $fp = fopen('php://output', 'wb');
        foreach ($csvArray as $line){
            fputcsv($fp, $line);
        }

        fclose($fp);
        exit;
    }

}
<?php


namespace AppBundle\Controller;

use AppBundle\Entity\ShopifyCheckout;
use AppBundle\Entity\ShopifyOrder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends DefaultController
{

    /**
     * @Route("/loadCheckouts", name="loadCheckouts")
     */
    public function loadCheckoutsAction(Request $request)
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
        $orders = $this->getRepository('ShopifyCheckout')->findAll();
//        foreach ($orders as $order){
//            $em->remove($order);
//        }
//        $em->flush();


        for($i=1;$i<1000;$i++){
            $ch = curl_init();

            if($shop->getCountrySelect()){
                curl_setopt($ch, CURLOPT_URL, $shop->getUrl().'/admin/checkouts.json?created_at_min=2019-05-24T00:00:00+02:00&limit=250&status=any&page='.$i);
            }else{
                curl_setopt($ch, CURLOPT_URL, $shop->getUrl().'/admin/checkouts.json?limit=250&status=any&page='.$i);
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
            $hostSplitText = explode("@",$shop->getUrl());
            $headers[] = 'Postman-Token: 38a4469f-c65b-4ac0-bdb4-0a1e911e9326,febb0004-d945-4dca-ba90-172f9631bb59';
            $headers[] = 'User-Agent: PostmanRuntime/7.20.1';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $orders = json_decode($result,true)['checkouts'];
            if(count($orders) == 0){
                var_dump($i);
                break;
            }else{
                foreach ($orders as $order){
                    try{
                        $customer = $order['customer'];
                    }catch (\Exception $e){
                        var_dump($order['id']);
                        var_dump($e->getMessage());
                        continue;
                    }

                    if(!$shop->getCountrySelect() || $order['customer_locale'] == "bg"){
                        $orderObj = new ShopifyCheckout();
                        $orderObj->setNumber($order['name']);
                        $orderObj->setAbandonedDate($order['created_at']);
                        $orderObj->setUpdatedAt($order['updated_at']);
                        $orderObj->setAmount($order['total_price']);
                        $orderObj->setFirstname($customer['first_name']);
                        $orderObj->setLastname($customer['last_name']);
                        $orderObj->setAcceptMarketing($customer['accepts_marketing']);
                        $orderObj->setEmail($order['email']);
                        $orderObj->setPhone($customer['accepts_marketing']);
                        if($shop->getNumberCorrection()) {
                            $orderObj->setPhone(str_replace(["+"," "],"",$customer['phone']));
                        }else{
                            $orderObj->setPhone($customer['phone']);
                        }
                        $orderObj->setShop($shopId);

                        if(key_exists('shipping_address',$order)){
                            $orderObj->setShippingCountry($order['shipping_address']['country']);
                        }

                        if(array_key_exists('billing_address',$order)){
                            if($shop->getNumberCorrection()) {
                                $orderObj->setCustomerPhone(str_replace(["+"," "],"",$order['billing_address']['phone']));
                            }else{
                                $orderObj->setCustomerPhone($order['billing_address']['phone']);
                            }
                        }
                        if(array_key_exists('shipping_address',$order)){
                            if($shop->getNumberCorrection()) {
                                $orderObj->setShippingPhone(str_replace(["+"," "],"",$order['shipping_address']['phone']));

                            }else{
                                $orderObj->setShippingPhone($order['shipping_address']['phone']);

                            }
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
            }

        }
        exit;
    }

    /**
     * @Route("/checkout/update", name="update_checkouts")
     */
    public function updateCheckouts(Request $request){
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
        $dates = $this->getRepository('ShopifyCheckout')->getLatestUpdateDate($shopId);
        $latestUpdatedDate = $dates['latest_updated_date'];
        $em = $this->getDoctrine()->getManager();
        $checkoutRepository = $this->getRepository('ShopifyCheckout');
//        var_dump($latestUpdatedDate);
        for($i=1;$i<1000;$i++){
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $shop->getUrl().'/admin/checkouts.json?updated_at_min='.$latestUpdatedDate.'&limit=250&status=any&page='.$i);
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
            $orders = json_decode($result,true)['checkouts'];
//            var_dump(count($orders));
            if(count($orders) == 0){
//                var_dump($i);
                break;
            }else{
                foreach ($orders as $order){


                    if(array_key_exists('customer',$order) and (!$shop->getCountrySelect() || $order['customer_locale'] == "bg")){
                        $customer = $order['customer'];
                        $orderObj = $checkoutRepository->findOneBy(array('number'=>$order['name']));
                        if($orderObj == null){
                            $orderObj = new ShopifyCheckout();
                        }
                        $orderObj->setNumber($order['name']);
                        $orderObj->setAbandonedDate($order['created_at']);
                        $orderObj->setUpdatedAt($order['updated_at']);
                        $orderObj->setAmount($order['total_price']);
                        $orderObj->setFirstname($customer['first_name']);
                        $orderObj->setLastname($customer['last_name']);
                        $orderObj->setAcceptMarketing($customer['accepts_marketing']);
                        $orderObj->setEmail($order['email']);
                        $orderObj->setPhone($customer['accepts_marketing']);
                        if(key_exists('shipping_address',$order)){
                            $orderObj->setShippingCountry($order['shipping_address']['country']);
                        }
                        if($shop->getNumberCorrection()) {
                            $orderObj->setPhone(str_replace(["+"," "],"",$customer['phone']));

                        }else{
                            $orderObj->setPhone($customer['phone']);
                        }
                        if(array_key_exists('billing_address',$order)){
                            if($shop->getNumberCorrection()) {
                                $orderObj->setCustomerPhone(str_replace(["+"," "],"",$order['billing_address']['phone']));
                            }else{
                                $orderObj->setCustomerPhone($order['billing_address']['phone']);
                            }
                        }
                        if(array_key_exists('shipping_address',$order)){
                            if($shop->getNumberCorrection()) {
                                $orderObj->setShippingPhone(str_replace(["+"," "],"",$order['shipping_address']['phone']));
                            }else{
                                $orderObj->setShippingPhone($order['shipping_address']['phone']);
                            }
                        }
                        $orderObj->setShop($shopId);

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
            }
//            var_dump($i);
        }
        return null;


    }

    /**
     * @Route("/checkouts/generate/csv", name="generate_csv_checkouts")
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
        $this->updateCheckouts($request);

        $orders = $this->getRepository('ShopifyCheckout')->findAll();

        $csvArray = array();
        $headers = array('STATUS','ORDER NUMBER','AMOUNT ABANDONED','DATE ABANDONED','ACCEPT MARKETING','FIRST NAME','LAST NAME','EMAIL','PHONE','CUSTOMER PHONE','SHIPPING PHONE','SHIPPING COUNTRY');
        $csvArray[] = $headers;
        foreach ($orders as $order){
            $csvLine = array();
            $csvLine[] = "";
            $csvLine[] = $order->getNumber();
            $csvLine[] = $order->getAmount();
            $csvLine[] = $order->getAbandonedDate();
            $csvLine[] = $order->getAcceptMarketing();
            $csvLine[] = $order->getFirstname();
            $csvLine[] = $order->getLastname();
            $csvLine[] = $order->getEmail();

            if($shop->getNumberCorrection()) {
                $phone = $this->getPhoneNumber($order->getPhone(),"359");
                $customerPhone =  $this->getPhoneNumber($order->getCustomerPhone(),"359");
                $shippingPhone = $this->getPhoneNumber($order->getShippingPhone(),"359");

            }else{
                $phone = $this->getPhoneNumber($order->getPhone(),"40");
                $customerPhone =  $this->getPhoneNumber($order->getCustomerPhone(),"40");
                $shippingPhone = $this->getPhoneNumber($order->getShippingPhone(),"40");
            }

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
        header('Content-Disposition: attachment; filename="'.$shop->getName().'checkouts.csv"');
        $fp = fopen('php://output', 'wb');
        foreach ($csvArray as $line){
            fputcsv($fp, $line);
        }

        fclose($fp);
        exit;
    }


}
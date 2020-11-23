<?php


namespace AppBundle\Controller;
use AppBundle\Entity\ShopifyOrder;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleSheetController extends DefaultController
{

    /**
     * @Route("/tag/fire", name="tag_fire")
     */
    public function tagFire(Request $request){
        $data = json_decode($request->getContent(), true);
        if($data != null){
            if(array_key_exists('tags',$data)){
                $tags = explode(",",$data['tags']);
                foreach ($tags as $tag){
                    if(strpos($tag, 'RMA') !== false){
                        $this->testCsvAction($request);
                        return new Response("google sheet updated");
                    }
                }
                return new Response("tags found, but not matched");

            }else{
                return new Response("no tags field found");
            }

        }else{
            return new Response("not webhook");
        }
    }


    /**
     * @Route("/csv/export", name="csv_export")
     */
    public function testCsvAction(Request $request){

        $shops = $this->getRepository('Shop')->findAll();
        foreach ($shops as $shop){
            $this->updateOrders($shop->getId(), $request);
        }

        $client = $this->getClient();
        $service = new Google_Service_Sheets($client);

//        $spreadsheetId = '1zaSvLXAUqHVmcdTeRxmUb66Eq9ejIAkapri5XF7QYeg';
        $spreadsheetId = '1r3saVpzffVDUczEkNt-T0ab48Le_6Ykh3U_OTlh9RkM';

        $range = 'OrdersList!A2:R';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $existingValues = $response->getValues();
        $existingValueMap = array();
        if($existingValues != null){
            foreach ($existingValues as $row){
                $existingValueMap[trim($row[0])] = $row;
            }
        }


        $range = 'OrdersList!A1';

        $values = [
            [
                "Order Number",
                "Shop name",
                "Link to ordessdsdr",
                "Shipping Country",
                "RMA#",
                "Waybill#",
                "First Name",
                "Last Name",
                "Customer Phone",
                "Operator Name", "STATUS Operator",
                "Възможни действия \n1. Замяна ( Отг. Склад) \n2. Ремонт ( Отг. Силвия) \n3. Връщане сума ( Отг. Мирослава)",
                "статус",
                "заприхождаване в склада (номер на доставка)",
                "изписване за замяна (номер поръчка)",
                "Номер товарителница за връшане към клиента",
                "(възст на сума) дата на платежно"
            ]
        ];

        $shops = $this->getRepository('Shop')->findAll();
        $shopMap = array();
        foreach ($shops as $shop){
            $shopMap[$shop->getId()] = $shop->getName();
        }

        $orders = $this->getRepository('ShopifyOrder')->getCSVExportOrders();
//        var_dump(count($orders));exit;
        $i = 1;
        foreach ($orders as $order){

            $rma = "";
            $tags = explode(",",$order->getTags());
            foreach ($tags as $tag){
                if(strpos($tag, 'RMA') !== false){
                    $rma = $tag;
                }
            }
            if($order->getPhone() != null){
                $phoneNumber = $this->getPhoneNumber($order->getPhone(),$this->getPhonePrefix(strtolower($order->getShippingCountry())));
            }else if($order->getCustomerPhone()){
                $phoneNumber = $this->getPhoneNumber($order->getCustomerPhone(),$this->getPhonePrefix(strtolower($order->getShippingCountry())));

            }else{
                $phoneNumber = $this->getPhoneNumber($order->getShippingPhone(),$this->getPhonePrefix(strtolower($order->getShippingCountry())));
            }
//            var_dump($order->getPhone());exit;
            $values[$i] = [
                $order->getOrderName(),
                $shopMap[$order->getShop()],
                $order->getOrderUrl(),
                $order->getShippingCountry() == null ? "" : $order->getShippingCountry(),
                $rma,
                $order->getWaybillId(),
                $order->getFirstname(),
                $order->getLastname(),
                $phoneNumber
            ];

            $orderName = trim($order->getOrderName());

            if(key_exists($orderName,$existingValueMap)){
                $insertedData = array_slice($existingValueMap[$orderName],9);
                $values[$i] = array_merge($values[$i],$insertedData);
            }else{
                $values[$i] = array_merge($values[$i],["","","","","","","","",""]);
            }

            $i+=1;
        }


        $updateBody = new Google_Service_Sheets_ValueRange([
            'range' => $range,
            'majorDimension' => 'ROWS',
            'values' => $values,
        ]);

        $valueInputOption = 'USER_ENTERED'; // Or RAW

        $params = [
            'valueInputOption' => $valueInputOption
        ];

        $clearBody = new \Google_Service_Sheets_ClearValuesRequest();

        $response = $service->spreadsheets_values->clear($spreadsheetId,'OrdersList', $clearBody);

        $result = $service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $updateBody,
            $params
        );
        var_dump($result);
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/googleSheet/export", name="all_orders_export")
     */
    public function googleSheetExportAction(Request $request){

        $shopId = $request->get('shop');
        if($shopId == null){
            var_dump("shop id not given");
            exit;
        }
        $shop = $this->getRepository('Shop')->find($shopId);

        $this->updateOrders($shop->getId(), $request);


        $client = $this->getClient();
        $service = new Google_Service_Sheets($client);

//        $spreadsheetId = '1zaSvLXAUqHVmcdTeRxmUb66Eq9ejIAkapri5XF7QYeg';
        $spreadsheetId = '1Rmum93YCVGpMmLZHMUDdRlhMfQ8wl3wgmLGJUKseYO4';


        $range = $shop->getAllOrdersSheetName().'!A1:AA';

        $values = [
            [
                "Shop name",
                "ORDER NUMBER",
                "Order date",
                "STAUS",
                "Payment",
                "Order link",
                "Order value",
                "ORDER TAGS",
                "Staff who made the order",
                "Tracking number",
                "Tracking url",
                "Order notes by customer",
                "Order notes by staff",
                "Customer email",
                "Shipping with",
                "Numbers of order of customer",
                "Customer's TTL spent",
                "Phone w country code",
                "BUYER",
                "CONTACT PERSON",
                "PHONE",
                "CITY/REGION",
                "POST CODE",
                "ADDRESS",
                "COUNTRY",
                "DESCRIPTION",
                "QUANTITIES"
            ]
        ];



//        $orders = $this->getRepository('ShopifyOrder')->findBy(array(),array(),2000);
        $orders = $this->getRepository('ShopifyOrder')->findBy(array('shop'=>$shopId));
//        var_dump(count($orders));exit;
        ini_set('memory_limit','2048M');

        $i = 2;
        foreach ($orders as $order){

            var_dump($i);

            if($order->getPhone() != null){
                $phoneNumber = $this->getPhoneNumber($order->getPhone(),$this->getPhonePrefix(strtolower($order->getShippingCountry())));
            }else if($order->getCustomerPhone()){
                $phoneNumber = $this->getPhoneNumber($order->getCustomerPhone(),$this->getPhonePrefix(strtolower($order->getShippingCountry())));

            }else{
                $phoneNumber = $this->getPhoneNumber($order->getShippingPhone(),$this->getPhonePrefix(strtolower($order->getShippingCountry())));
            }
//            var_dump($order->getPhone());exit;
            $values[] = [
                $shop->getName(),
                $order->getOrderName(),
                $order->getCreatedAt(),
                $order->getFulfillmentStatus() == null ? "Unfulfilled" : $order->getFulfillmentStatus(),
                $order->getFinancialStatus() == null ? "" : $order->getFinancialStatus(),
                $order->getOrderUrl(),
                $order->getAmount(),
                $order->getTags(),
                "",
                $order->getWaybillId() == null ? "" : $order->getWaybillId(),
                $order->getTrackingUrl() == null ? "" : $order->getTrackingUrl(),
                $order->getCustomerNote() == null ? "" : $order->getCustomerNote(),
                $order->getStaffNote() == null ? "" : $order->getStaffNote(),
                $order->getEmail() == null ? "" : $order->getEmail(),
                '=VLOOKUP(Y'.$i.',Couriers!$B$4:$C,2,TRUE)',
                $order->getOrderCount(),
                $order->getTotalSpend(),
                $phoneNumber,
                $order->getFirstname(). " ".$order->getFirstname(),
                $order->getFirstname(). " ".$order->getLastname(),
                $order->getCustomerPhone() == null ? "" : $order->getCustomerPhone(),
                $order->getCity() == null ? "" : $order->getCity(),
                $order->getZip() == null ? "" : $order->getZip(),
                $order->getOrderAddress() == null ? "" : $order->getOrderAddress(),
                $order->getCountryCode() == null ? "" : $order->getCountryCode(),
                $order->getDescription() == null ? "" : $order->getDescription(),
                $order->getProductCount() == null ? "" : $order->getProductCount(),
            ];

//            $orderName = trim($order->getOrderName());
//
//            if(key_exists($orderName,$existingValueMap)){
//                $insertedData = array_slice($existingValueMap[$orderName],9);
//                $values[$i] = array_merge($values[$i],$insertedData);
//            }else{
//                $values[$i] = array_merge($values[$i],["","","","","","","","",""]);
//            }

            $i+=1;
        }


        $updateBody = new Google_Service_Sheets_ValueRange([
            'range' => $range,
            'majorDimension' => 'ROWS',
            'values' => $values,
        ]);

        $valueInputOption = 'USER_ENTERED'; // Or RAW

        $params = [
            'valueInputOption' => $valueInputOption
        ];

        $clearBody = new \Google_Service_Sheets_ClearValuesRequest();

        $response = $service->spreadsheets_values->clear($spreadsheetId,$range, $clearBody);

        $result = $service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $updateBody,
            $params
        );
        var_dump($result);
        return $this->redirectToRoute('dashboard');
    }



    protected function getClient()
    {
        $client = new Google_Client();
//        $client->setAccessToken("AIzaSyDcb74sCkh-8WoFYHXFtbylYe_ESlG_fvA");
//        return $client;
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
//        var_dump($this->getParameter('web_dir').'/credentials.json');exit;
        $client->setAuthConfig($this->getParameter('web_dir').'/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = $this->getParameter('web_dir').'/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }else{
            var_dump("error");exit;
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.

            $refreshToken = $client->getRefreshToken();


            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }

            $updatedToken = $client->getAccessToken();
            $updatedToken['refresh_token'] = $refreshToken;

            file_put_contents($tokenPath, json_encode($updatedToken));
        }
        return $client;
    }

    /**
     * @Route("/update/orders/force/{shop}", name="update_orders_force")
     */
    public function updateOrders($shop,Request $request){

        if($request->get('shop') != null){
            $shopId = $request->get('shop');
        }else{
            $shopId = $shop;
        }
        if($shopId == null){
            var_dump("shop id not given");
            exit;
        }
        $shop = $this->getRepository('Shop')->find($shopId);
        if($shop == null){
            var_dump("shop not exists");
            exit;
        }

        if($request->get('date') != null){
            $latestUpdatedDate = $request->get('date');
        }else{
            $dates = $this->getRepository('ShopifyOrder')->getLatestUpdateDate($shop->getId());
            $latestUpdatedDate = $dates['latest_updated_date'];
        }

        var_dump($shopId);
        var_dump($latestUpdatedDate);
//        exit;

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
            try{
                $orders = json_decode($result,true)['orders'];
            }catch (\Exception $e){
                var_dump($result);
                exit;
            }
//            var_dump(count($orders));exit;
            if(count($orders) == 0){
//                var_dump($i);
                break;
            }else{
                foreach ($orders as $order){

                    if(array_key_exists('customer',$order)){
                        $customer = $order['customer'];
                    }else{
                        continue;
                    }

//                    $customer = $order['customer'];

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
                        $orderObj->setFinancialStatus($order['financial_status']);
                        $orderObj->setAcceptMarketing($customer['accepts_marketing']);
                        $orderObj->setAmount($order['total_price']);
                        $orderObj->setCustomerId((string)$customer['id']);
                        $orderObj->setFirstname($customer['first_name']);
                        $orderObj->setLastname($customer['last_name']);
                        $orderObj->setOrderCount($customer['orders_count']);
                        $orderObj->setTotalSpend($customer['total_spent']);
                        $orderObj->setLastOrderId((string)$customer['last_order_id']);
                        $orderObj->setEmail($customer['email']);
                        $orderObj->setCustomerNote($customer['note']);
                        $orderObj->setStaffNote($order['note']);
                        $orderObj->setOrderUrl($order['order_status_url']);
                        $orderObj->setOrderName($order['name']);
                        $orderObj->setTags($order['tags']);
                        if(is_array($order['fulfillments']) && count($order['fulfillments']) > 0){
                            $orderObj->setWaybillId($order['fulfillments'][0]['tracking_number']);
                            $orderObj->setTrackingUrl($order['fulfillments'][0]['tracking_url']);
                        }else{
                            $orderObj->setWaybillId("");
                        }
                        if(key_exists('shipping_address',$order)){
                            $orderObj->setShippingCountry($order['shipping_address']['country']);
                            $orderObj->setCity($order['shipping_address']['city']);
                            $orderObj->setZip($order['shipping_address']['zip']);

                            $address = $order['shipping_address']['address1'];

                            if($order['shipping_address']['address2'] != ''){
                                $address.= ', '.$order['shipping_address']['address2'];
                            }
                            $orderObj->setOrderAddress($address);
                            $orderObj->setCountryCode($order['shipping_address']['country_code']);
                        }

                        $description = "";
                        $totalProducts = 0;

                        if(key_exists('line_items',$order)){

                            foreach ($order['line_items'] as $line_item) {
                                $description.= $line_item['title']. " - ".$line_item['quantity']."\n";
                                $totalProducts += $line_item['quantity'];

                            }
                        }

                        $orderObj->setDescription($description);
                        $orderObj->setProductCount($totalProducts);



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
}
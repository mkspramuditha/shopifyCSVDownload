<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ShopifyOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    protected $apiUrl = "https://2354d58135ab061a6b441b5631a4b2b8:2feae715e670be2696d1f8f61a9a14c8@hillman.myshopify.com";


    protected function getRepository($class)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:' . $class);
        return $repo;
    }

    protected function insert($object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();
    }

    protected function remove($object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();
    }

    protected function getPhoneNumber($number){
        $number = preg_replace("/[^0-9]/", "",$number);
        $firstNumber = substr($number, 0, 1);
        $number = preg_replace("/^0+/","359",$number);
        $query = "359";

        if(substr($number, 0, strlen($query)) != $query && $number!= "" && $number != null){
            $number = "359".$number;
        }

        if(strlen($number) > 12){
            $number = substr($number,-12);
        }
        return $number;
    }

    /**
     * @Route("/test", name="test")
     */
    public function testAction(Request $request){
        $orderId = $request->get('order');
        $this->updateOrder($orderId);
//        var_dump($this->getPhoneNumber('359876654705'));
        exit;
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardAction(Request $request){
        $shops = $this->getRepository('Shop')->findAll();
        return $this->render('default/index.html.twig',array(
            'shops'=> $shops
        ));
    }

    private function updateOrder($orderId){


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://2354d58135ab061a6b441b5631a4b2b8:2feae715e670be2696d1f8f61a9a14c8@hillman.myshopify.com/admin/orders/'.$orderId.'.json');
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
        $order = json_decode($result,true)['order'];

        if($order == null){
            return null;
        }


        $customer = $order['customer'];


        if($customer['default_address']['country'] == "Bulgaria"){
            $orderObj = $this->getRepository('ShopifyOrder')->findOneBy(array('orderId'=>$orderId));
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
            $this->insert($orderObj);
            var_dump($orderObj->getPhone());
            var_dump($orderObj->getShippingPhone());
            var_dump($orderObj->getCustomerPhone());

        }

        exit;
    }
}

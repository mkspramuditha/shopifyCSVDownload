<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ShopifyOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{


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
        return $number;
    }

    /**
     * @Route("/test", name="test")
     */
    public function testAction(){
        var_dump($this->getPhoneNumber('359876654705'));
        exit;
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardAction(Request $request){
        return $this->render('default/index.html.twig');
    }
}

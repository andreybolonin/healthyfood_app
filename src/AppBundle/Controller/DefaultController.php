<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/lp.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/tariff", name="tariff")
     */
    public function tariffAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/tariff.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/delivery/{tariff}", name="delivery")
     */
    public function deliveryAction(Request $request)
    {
        $tariff = $request->get('tariff');
        $fmt = new \IntlDateFormatter( "ru_RU", \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Europe/Kiev');



        $delivery = [
            date_format(new \DateTime('+2 days'), 'Y-m-d') => $fmt->format(new \DateTime('+2 days')),
            date_format(new \DateTime('+5 days'), 'Y-m-d') => $fmt->format(new \DateTime('+5 days')),
            date_format(new \DateTime('+7 days'), 'Y-m-d') => $fmt->format(new \DateTime('+7 days')),
        ];

//        var_dump($delivery);exit;

        // replace this example code with whatever you need
        return $this->render('default/delivery.html.twig', [
            'tariff' => $tariff,
            'delivery' => $delivery
        ]);
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function paymentAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/payment.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}

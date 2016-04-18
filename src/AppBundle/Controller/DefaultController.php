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
        return $this->render('default/lp.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/tariff", name="tariff")
     */
    public function tariffAction(Request $request)
    {
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

        $private_key = 'Lfj88TBWC2wCc9T8vCm2ZunA5qBKkR8SZAKcN0h0';
        $json_string = [
            'version' => 3,
            'public_key' => 'i21026092163',
            'action' => 'subscribe',
            'amount' => '1800',
            'currency' => 'UAH',
            'description' => 'test description',
            'order_id' => 'test order_id'
        ];

        $data = base64_encode(json_encode($json_string));

        $signature = base64_encode(sha1($private_key.$data.$private_key, 1));

        return $this->render('default/delivery.html.twig', [
            'tariff' => $tariff,
            'delivery' => $delivery,
            'data' => $data,
            'signature' => $signature
        ]);
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function paymentAction(Request $request)
    {
        // save delivery data &
        // redirect post to https://www.liqpay.com/api/3/checkout
        
        return $this->render('default/payment.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}

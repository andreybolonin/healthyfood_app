<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Goutte\Client;
use Symfony\Component\HttpKernel\HttpKernelInterface;

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

        return $this->render('default/delivery.html.twig', [
            'tariff' => $tariff,
            'delivery' => $delivery,
        ]);
    }

    /**
     * @Route("/payment/{tariff}", name="payment")
     */
    public function paymentAction(Request $request)
    {
        // save delivery data &
        $em = $this->getDoctrine()->getManager();
        $lead = new Lead();

        $lead->setName($request->get('name'));
        $lead->setSurname($request->get('surname'));
        $lead->setAddress($request->get('address'));
        $lead->setBuilding($request->get('building'));
        $lead->setEmail($request->get('email'));
        $lead->setPhone($request->get('phone'));
        $lead->setTariff($request->get('tariff'));
        $lead->setDeliveryDate($request->get('delivery_date'));

        $em->persist($lead);
        $em->flush();

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

//        $subRequest = Request::create('https://www.liqpay.com/api/3/checkout', 'POST', ['data' => $data, 'signature' => $signature]);
//        return new RedirectResponse($subRequest);
//        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);

//        $client = new Client();
//        $crawler = $client->request('POST', 'https://www.liqpay.com/api/3/checkout', [
//            'data' => $data,
//            'signature' => $signature
//        ]);

//        $this->sendHttpRequest();

//       $this->redirect_post('https://www.liqpay.com/api/3/checkout', ['data' => $data, 'signature' => $signature]);

//        return $this->redirect();

//        return $this->redirectToRoute('https://www.liqpay.com/api/3/checkout', ['data' => $data,
//            'signature' => $signature], 301);
        
//        return new RedirectResponse('https://www.liqpay.com/api/3/checkout', 302, )

        // redirect post to https://www.liqpay.com/api/3/checkout
        
        return $this->render('default/payment.html.twig', [
            'data' => $data,
            'signature' => $signature
        ]);
    }


    /**
     * Redirect with POST data.
     *
     * @param string $url URL.
     * @param array $data
     * @param array $headers Optional. Extra headers to send.
     * @throws \Exception
     * @internal param array $post_data POST data. Example: array('foo' => 'var', 'id' => 123)
     */
    public function redirect_post($url, array $data, array $headers = null) {
        $params = array(
            'http' => array(
                'method' => 'POST',
                'header'  => 'Content-type: multipart/form-data',
                'content' => http_build_query($data)
            )
        );
        if (!is_null($headers)) {
            $params['http']['header'] = '';
            foreach ($headers as $k => $v) {
                $params['http']['header'] .= "$k: $v\n";
            }
        }
        $ctx = stream_context_create($params);
        $fp = fopen($url, 'rb', false, $ctx);
        if ($fp) {
            echo stream_get_contents($fp);
            die();
        } else {
            // Error
            throw new \Exception("Error loading '$url', $php_errormsg");
        }
    }

    function do_post_request($url, $data, $optional_headers = null)
    {
        $params = array('http' => array(
            'method' => 'POST',
            'content' => $data
        ));
        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new \Exception("Problem with $url");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new \Exception("Problem reading data from $url");
        }
        return $response;
    }

    private function sendHttpRequest($host, $path, $query, $port = 80)
    {
        header("POST $path HTTP/1.1\r\n" );
        header("Host: $host\r\n" );
        header("Content-type: application/x-www-form-urlencoded\r\n" );
        header("Content-length: " . strlen($query) . "\r\n" );
        header("Connection: close\r\n\r\n" );
        header($query);
    }
}

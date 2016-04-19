<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lead;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/tariff/", name="tariff")
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
            'delivery' => $delivery
        ]);
    }

    /**
     * Create order and prepare payment data
     * 
     * @Route("/payment/{tariff}", name="payment")
     */
    public function paymentAction(Request $request)
    {
        // save delivery data
        $em = $this->getDoctrine()->getManager();
        $lead = new Lead();

        $lead->setName($request->get('name'));
        $lead->setSurname($request->get('surname'));
        $lead->setAddress($request->get('address'));
        $lead->setBuilding($request->get('building'));
        $lead->setEmail($request->get('email'));
        $lead->setPhone($request->get('phone'));
        $lead->setTariff($request->get('tariff'));
        $lead->setAmount($lead->calculateAmount());
        $lead->setCurrency('UAH');
        $lead->setDeliveryDate($request->get('delivery'));
        $lead->setPayed(false);

        $em->persist($lead);
        $em->flush();

        // generate payment data
        $private_key = 'Lfj88TBWC2wCc9T8vCm2ZunA5qBKkR8SZAKcN0h0';
        $json_string = [
            'version' => 3,
            'public_key' => 'i21026092163',
            'action' => 'pay',
            'amount' => $lead->getAmount(),
            'currency' => 'UAH',
            'description' => 'Оплата услуг HealthyFood',
            'order_id' => $lead->getId(),
            'server_url' => '',
            'result_url' => 'http://thehealthyfood.ru/thx',
            'sandbox' => 1
        ];

        $data = base64_encode(json_encode($json_string));
        $signature = base64_encode(sha1($private_key.$data.$private_key, 1));

        $lead->setData($data);
        $lead->setSignature($signature);

        $em->persist($lead);
        $em->flush();

        $fmt = new \IntlDateFormatter( "ru_RU", \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Europe/Kiev');
        $delivery = $fmt->format(new \DateTime($request->get('delivery')));

        return $this->render('default/payment.html.twig', [
            'data' => $data,
            'signature' => $signature,
            'tariff' => $request->get('tariff'),
            'delivery' => $delivery
        ]);
    }

    /**
     * @Route("/thx", name="thx")
     */
    public function thxAction(Request $request)
    {
        return $this->render('default/thx.html.twig', []);
    }

    /**
     * @Route("/callback", name="callback")
     */
    public function callbackAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lead = $this->getDoctrine()->getRepository('AppBundle:Lead')->find($request->get('order_id'));

        $private_key = 'Lfj88TBWC2wCc9T8vCm2ZunA5qBKkR8SZAKcN0h0';
        $json_string = [
            'version' => 3,
            'public_key' => 'i21026092163',
            'action' => 'pay',
            'amount' => $lead->getAmount(),
            'currency' => 'UAH',
            'description' => 'Оплата услуг HealthyFood',
            'order_id' => $lead->getId(),
            'server_url' => '',
            'result_url' => 'http://thehealthyfood.ru/thx',
            'sandbox' => 1
        ];

        $data = base64_encode(json_encode($json_string));
        $signature = base64_encode(sha1($private_key.$data.$private_key, 1));

        // Verify callback
        if ($data == $request->get('data') && $signature == $request->get('signature')) {

            $lead->setStatus($request->get('status'));
            $lead->setType($request->get('type'));

            if ($request->get('status') == 'success') {
                $lead->setPayed(true);
            }

            $em->persist($lead);
            $em->flush();
        }

        return [];
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

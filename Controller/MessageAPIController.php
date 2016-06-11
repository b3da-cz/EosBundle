<?php

namespace b3da\EasyOpenSslBundle\Controller;

use b3da\EasyOpenSslBundle\Entity\Client;
use b3da\EasyOpenSslBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MessageAPIController extends Controller
{
    /**
     * @Route("/msg-api/client/create/", name="b3da.eos.msg_api.client.create")
     * @Method("POST")
     */
    public function clientCreateAction()
    {
        $eos = $this->get('b3da_easy_open_ssl.eos');
        $client = new Client();
        $client = $eos->generateKeyPairForClient($client);

        if (!$client) {
            $responseData = [
                'status' => 'error',
                'details' => 'key pair generating failed'
            ];
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            $responseData = [
                'status' => 'success',
                'id' => $client->getId()
            ];
        } catch (\Exception $e) {
            $responseData = [
                'status' => 'error',
                'details' => 'client persisting failed'
            ];
        }

        $response = new JsonResponse($responseData);
        return $response;
    }

    /**
     * @Route("/msg-api/client/import-public/", name="b3da.eos.msg_api.client.import_public")
     * @Method("POST")
     */
    public function clientImportPublicKeyAction(Request $request)
    {
        $importData = $request->request->get('data');

        if (!$importData) {
            $response = new JsonResponse([
                'status' => 'error',
                'details' => 'no data to import'
            ]);
            return $response;
        }
        if (!is_array($importData)) {
            $importData = json_decode($importData);
        }

        if (!$importData['pubkey'] || $importData['id']) {
            $response = new JsonResponse([
                'status' => 'error',
                'details' => 'wrong data format'
            ]);
            return $response;
        }

        $client = new Client();
        $client->setId($importData['id']);
        $client->setPubKey(base64_decode($importData['pubkey']));

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            $responseData = [
                'status' => 'success',
                'id' => $client->getId()
            ];
        } catch (\Exception $e) {
            $responseData = [
                'status' => 'error',
                'details' => 'client persisting failed'
            ];
        }

        $response = new JsonResponse($responseData);
        return $response;
    }

    /**
     * @Route("/msg-api/client/export-public/{id}/", name="b3da.eos.msg_api.client.export_public")
     * @Method("GET")
     */
    public function clientExportPublicKeyAction($id)
    {
        $client = $this->getDoctrine()->getRepository(Client::class)->find($id);

        if (!$client) {
            $responseData = [
                'status' => 'error',
                'details' => 'no client for id'
            ];
        } else {
            $responseData = [
                'status' => 'success',
                'data' => [
                    'id' => $client->getId(),
                    'pubkey' => base64_encode($client->getPubKey())
                ]
            ];
        }

        $response = new JsonResponse($responseData);
        return $response;
    }

    /**
     * @Route("/msg-api/msg/encrypt/{clientId}/{data}/", name="b3da.eos.msg_api.msg.encrypt")
     * @Method("GET")
     */
    public function messageEncryptAction($clientId, $data)
    {
        $client = $this->getDoctrine()->getRepository(Client::class)->find($clientId);

        if (!$client) {
            $responseData = [
                'status' => 'error',
                'details' => 'no client for id'
            ];
        } else {
            $eos = $this->get('b3da_easy_open_ssl.eos');
            $message = $eos->encrypt($client, $data);
            $responseData = [
                'status' => 'success',
                'data' => [
                    'message' => $message->getValues(true)
                ]
            ];
        }

        $response = new JsonResponse($responseData);
        return $response;
    }

    /**
     * @Route("/msg-api/msg/decrypt/{data}/", name="b3da.eos.msg_api.msg.decrypt")
     * @Method("GET")
     */
    public function messageDecryptAction($data)
    {
        if (!is_array($data)) {
            $data = json_decode($data);
        }

        if (!$data->message) {
            $responseData = [
                'status' => 'error',
                'details' => 'wrong format'
            ];
        } else {
            $eos = $this->get('b3da_easy_open_ssl.eos');
            $message = new Message();
//            exit(dump($data->data->message));
//            $tmpArr = [
//                'id' => $data->data->message->id,
//                'data' => $data->data->message->data,
//                'iv' => $data->data->message->iv,
//                'key' => $data->data->message->key,
//                'client_id' => $data->data->message->client_id,
//
//            ];
            $message->setValues($data->message);
            $decryptedData = $eos->decrypt($message);

            $responseData = [
                'status' => 'success',
                'data' => $decryptedData
            ];
        }

        $response = new JsonResponse($responseData);
        return $response;
    }
}

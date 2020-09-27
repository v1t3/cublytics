<?php
declare(strict_types=1);

namespace App\Service;

use App\AppRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class CoubAuthService
 *
 * @package App
 */
class CoubAuthService
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * CoubAuthService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->client = new Client();
        $this->entityManager = $entityManager;
    }

    /**
     *
     * @param string $code
     *
     * @return array|bool|float|int|object|string|null
     * @throws GuzzleException
     * @throws Exception
     */
    public function getUserToken(string $code)
    {
        $responseData = [];

        if (
            '' === (string)$_ENV['COUB_KEY']
            && '' === (string)$_ENV['COUB_SECRET']
            && '' === $code
        ) {
            throw new Exception('Не заданы поля env or code');
        }

        $response = $this->client->request(
            'POST',
            AppRegistry::REQUEST_ACCESS_TOKEN,
            [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'redirect_uri'  => AppRegistry::REDIRECT_CALLBACK,
                    'client_id'     => $_ENV['COUB_KEY'],
                    'client_secret' => $_ENV['COUB_SECRET'],
                    'code'          => $code
                ]
            ]
        );

        if ($response->getStatusCode() === 200) {
            try {
                $responseData = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
            } catch (Exception $exception) {
                return [
                    'success' => false,
                    'error'   => [
                        'code'    => 0,
                        'message' => 'Неизвестный ответ от сервиса'
                    ]
                ];
            }

            if (!isset($responseData['access_token'])) {
                if (isset($tokenData['error'])) {
                    throw new Exception(
                        $responseData['error'],
                        $responseData['error_description']
                    );
                }

                throw new Exception(
                    'Неизвестный ответ от сервиса'
                );
            }
        }

        return $responseData;
    }

}
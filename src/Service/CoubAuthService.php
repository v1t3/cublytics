<?php
declare(strict_types=1);

namespace App\Service;

use App\AppRegistry;
use App\Entity\AccessList;
use App\Entity\CoubAuthorization;
use App\Entity\User;
use App\Repository\AccessListRepository;
use App\Repository\CoubAuthorizationRepository;
use App\Repository\UserRepository;
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
                throw new Exception(
                    $exception->getCode(),
                    $exception->getMessage()
                );
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

    /**
     * @param $data
     *
     * @return bool
     * @throws Exception
     */
    public function checkAccessGranted($data): bool
    {
        # если проверка отключена
        if (
            $_ENV['ACCESS_BY_LIST']
            && 'true' !== $_ENV['ACCESS_BY_LIST']
        ) {
            return true;
        }

        $channels = [];

        if (!empty($data) && !empty($data['channels'])) {

            /**
             * @var $userRepo UserRepository
             */
            $userRepo = $this->entityManager->getRepository(User::class);
            $user = $userRepo->findOneByUserId($data['id']);
            # пользователь уже проходил регистрацию
            if ($user) {
                return true;
            }

            # выберем только названия каналов
            foreach ($data['channels'] as $channel) {
                $channels[] = $channel['permalink'];
            }

            /**
             * @var $accessListRepo AccessListRepository
             */
            $accessListRepo = $this->entityManager->getRepository(AccessList::class);
            $accessList = $accessListRepo->findAll();

            foreach ($accessList as $item) {
                if (in_array($item->getUser(), $channels)) {
                    $item->setIsRegistered(true);

                    $this->entityManager->persist($item);
                    $this->entityManager->flush();

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $username
     * @param string $token
     *
     * @return bool
     * @throws Exception
     */
    public function setUserToken(string $username, string $token)
    {
        if ('' !== $username && '' !== $token) {
            /**
             * @var $coubAuthRepo CoubAuthorizationRepository
             */
            $coubAuthRepo = $this->entityManager->getRepository(CoubAuthorization::class);
            $coubAuth = $coubAuthRepo->findOneBy(['last_username' => $username]);

            if ($coubAuth) {
                $coubAuth->setToken($token);
            } else {
                $coubAuth = new CoubAuthorization();
                $coubAuth->setLastUsername($username);
                $coubAuth->setToken($token);
            }

            $this->entityManager->persist($coubAuth);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }


}
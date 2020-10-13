<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Mailer
 *
 * @package App\Service
 */
class Mailer
{
    /**
     *
     */
    public const FROM_ADDRESS = 'service@test.test';

    /**
     * @var Swift_Mailer
     */
    private Swift_Mailer $mailer;

    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * Mailer constructor.
     *
     * @param Swift_Mailer $mailer
     * @param Environment  $twig
     */
    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param User   $user
     * @param string $email
     * @param string $code
     *
     * @return bool
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function sendConfirmationMessage(User $user, string $email, string $code)
    {
        if ('' === $email || '' === $code) {
            throw new Exception('Не заданы обязательные параметры');
        }

        $messageBody = $this->twig->render(
            'security/confirmation.html.twig',
            [
                'user' => $user,
                'code' => $code
            ]
        );

        $message = new Swift_Message();
        $message
            ->setSubject('Подтверждение email')
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($email)
            ->setBody($messageBody, 'text/html');

        $this->mailer->send($message);

        return true;
    }
}
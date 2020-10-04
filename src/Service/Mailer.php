<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

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
     * @param Swift_Mailer           $mailer
     * @param Environment            $twig
     */
    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param User   $user
     * @param string $email
     *
     * @return bool
     * @throws Exception
     */
    public function sendConfirmationMessage(User $user, string $email)
    {
        if ('' === $email) {
            throw new Exception('Не задан email');
        }

        $messageBody = $this->twig->render(
            'security/confirmation.html.twig',
            [
                'user' => $user
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
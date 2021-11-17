<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use App\Entity\User;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    const ACCOUNT_CONFIRMATION = 'Welcome to Snowtricks !';
    const PASSWORD_RESET = 'Password reinitialization.';

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle mail sending for registration, forgot password and trick report features.
     *
     * @return void
     */
    public function sendMail(string $type, User $user, array $data)
    {
        try {
            $subject = mb_strtoupper($type);
            $message = (new TemplatedEmail())
                ->from(new Address('marialalij@gmail.com', 'No-reply Snowtricks'))
                ->to(new Address($user->getEmail(), $user->getUserName()))
                ->context($data)
                ->htmlTemplate('email/' . $type . '.html.twig');

            switch ($subject) {
                case 'ACCOUNT_CONFIRMATION':
                    $message->subject(self::ACCOUNT_CONFIRMATION);
                    break;
            }

            $this->mailer->send($message);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function send($email, $token)
    {
        $email = (new TemplatedEmail())
            ->from('marialalij@gmail.com')
            ->to(new Address($email))
            ->subject('Thanks for signinsg up!')

            // path of the Twig template to render
            ->htmlTemplate('email/registration.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'token' => $token,
            ]);

        $this->mailer->send($email);
    }
}

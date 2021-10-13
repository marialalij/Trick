<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var ImageService
     */
    private $imageService;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $tokenGenerator)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Handle password update.
     *
     * @return void
     */
    public function PasswordUpdate(User $user, string $password)
    {
        try {
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $password)
            );
            $user->setToken(null);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Handle reset password token generation.
     *
     * @return String $token
     */
    public function ResetPassword(User $user)
    {
        try {
            $token = $this->tokenGenerator->generateToken();
            $user->setToken($token);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $token;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Handle user profile edition.
     *
     * @return void
     */
    public function handleProfileEdition(User $user, FormInterface $form)
    {
        try {
            $this->entityManager->persist($user, $form);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}

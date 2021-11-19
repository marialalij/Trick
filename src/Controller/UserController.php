<?php

namespace App\Controller;

use App\Form\EmailResetType;
use App\Form\ResetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Service\Mailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\UserService;
use App\Form\NewPasswordType;
use App\Form\PasswordFormType;
use App\Form\ResetPasswordType;
use App\Form\UserType;



class UserController extends AbstractController
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var Mailer
     */

    private $mailer;

    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * @var UserService
     */
    private $userService;



    public function __construct(UserPasswordEncoderInterface $passwordEncoder, Mailer $mailer, UserRepository $userRepository, UserService $userService)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * @Route("/register", name="register")
     */

    public function register(Request $request): Response

    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $form->get("password")->getData())
            );
            $user->setToken($this->generateToken());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->mailer->send($user->getEmail(), $user->getToken());
            $this->addFlash("success", "Inscription réussie !");
        }
        return $this->render('registration/register.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/confirmer-mon-compte/{token}", name="confirm_account")
     * @param string $token
     */

    public function confirmAccount(string $token)
    {
        $user = $this->userRepository->findOneBy(["token" => $token]);
        if ($user) {
            $user->setToken(null);
            $user->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "Inscription réussie !");
            return $this->redirectToRoute("app_login");
        } else {
            $this->addFlash("error", "Ce compte n'exsite pas !");
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */

    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * Display all tricks
     *
     * @Route("user/users", name="user.users")
     */
    public function users(): Response
    {

        $rep = $this->getDoctrine()->getRepository(User::class);
        $users = $rep->findAll();

        return $this->render("pages/users.html.twig", [
            'users' => $users,

        ]);
    }

    /**
     * modifier le mot de passe
     *
     * @Route("/user/reset_password", name="user.resetPass")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(PasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($passwordEncoder->isPasswordValid($this->getUser(), $form->get('oldPassword')->getData())) {
                $this->userService->PasswordUpdate($user, $form->get('plainPassword')->getData());

                return $this->redirectToRoute('user.resetPass');
            }
            $this->addFlash('danger', 'le mot de passe invalide');

            return $this->redirectToRoute('user.resetPass');
        }

        return $this->render('security/resetPassword.html.twig', [
            'user' => $user,

            'form' => $form->createView(),
        ]);
    }

    /**
     *  forgot password reset link mail send.
     * @Route("/forgot_password_link", name="app_forgotten_password")
     *
     * @return Response
     */
    public function passwordLink(Request $request)
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $data = $form->getData();
            $user = $this->userRepository->findOneBy(['userName' => $data['userName']]);

            if (!$user) {
                $this->addFlash('danger', 'Aucun compte est associé à ce nom d utilisateur');

                return $this->redirectToRoute('app_forgotten_password');
            }

            $token = $this->userService->ResetPassword($user);
            $url = $this->generateUrl('app_new_password', ['token' => $token]);
            $this->mailer->sendMail('password_reset', $user, ['url' => $url]);

            $this->addFlash('success', 'un email de renitialisation envoyée');

            return $this->redirectToRoute('app_forgotten_password');
        }

        return $this->render('security/forgotPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * new password.
     *
     * @Route("/new_password/{token}", name="app_new_password")
     *
     * @param string $token
     *
     * @return Response
     */
    public function newPassword($token, Request $request)
    {
        $user = $this->userRepository->findOneBy(['token' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Invalide token');

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->PasswordUpdate($user, $form->get('plainPassword')->getData());

            return $this->redirectToRoute('home');
        }

        return $this->render('security/newPassword.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * Display user profile.
     * @Route("/user/profile/{userName}", name="user.profile")
     */
    public function profile(User $user): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'nav' => 'profile',
        ]);
    }


    /**
     * Handle user profile edition.
     * @Route("/user/edit/{userName}", name="user.edit")
     * @param $user User 
     */
    public function edit(User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);

        if ($this->userService->handleProfileEdition($user, $form) === true) {

            return $this->redirectToRoute('user.profile', [
                'userName' => $user->getUsername(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}

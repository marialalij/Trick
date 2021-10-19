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
            $this->addFlash("success", "Compte actif !");
            return $this->redirectToRoute("home");
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
    public function tricks(): Response
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

                $this->addFlash('success', 'Your password has been updated !');

                return $this->redirectToRoute('user.resetPass');
            }
            $this->addFlash('danger', 'Your old password is not valid');

            return $this->redirectToRoute('user.resetPass');
        }

        return $this->render('security/resetPassword.html.twig', [
            'user' => $user,
            'nav' => 'resetPass',
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
                $this->addFlash('danger', 'No account is associated with this username.');

                return $this->redirectToRoute('app_forgotten_password');
            }

            $token = $this->userService->ResetPassword($user);
            $url = $this->generateUrl('app_new_password', ['token' => $token]);
            $this->mailer->sendMail('password_reset', $user, ['url' => $url]);

            $this->addFlash('success', 'A confirmation link has been sent to your email. Please follow the link to choose a new password !');

            return $this->redirectToRoute('home');
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
            $this->addFlash('danger', 'Invalid token');

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(NewPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->PasswordUpdate($user, $form->get('plainPassword')->getData());
            $this->addFlash('success', 'Your password has been updated !');

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
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);

        if ($this->userService->handleProfileEdition($user, $form) === true) {

            return $this->redirectToRoute('user.profile', [
                'userName' => $user->getUserName(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'nav' => 'profile',
            'form' => $form->createView(),
        ]);
    }
}

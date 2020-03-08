<?php

namespace App\Controller;

use App\Entity\ResetRequest;
use App\Entity\Usersite;
use App\Form\LoginType;
use App\Form\ResetPasswordType;
use App\Form\ResetRequestType;
use App\Service\Mailer;
use App\Service\Security;
use App\Utils\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Login controller
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $loginModel = new Usersite();
        $form = $this->createForm(LoginType::class, $loginModel);
        $form->get('emailCanonical')->setData($lastUsername);

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Generate a request for resetting the password
     *
     * @param Request $request
     * @param Security $security
     * @param Mailer $mailer
     *
     * @return Response
     * @throws \Exception
     */
    public function resetRequest(Request $request, Security $security, Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new Usersite();
        $form = $this->createForm(ResetRequestType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                // Get the user
                $userFound = $em->getRepository('App:Usersite')->findOneBy([
                    'emailCanonical' => strtolower($user->getEmail()),
                ]);

                if (null === $userFound) {
                    $this->addFlash(Constants::FLASH_WARNING, 'L\'email n\'ai rattaché à aucun compte');
                    return $this->redirectToRoute($request->get('_route'));
                }

                // Check if a reset request already exists from the user
                $oldRequest = $em->getRepository('App:ResetRequest')->findOneBy(['idUsersite' => $userFound]);
                if (null !== $oldRequest) {
                    $this->addFlash(Constants::FLASH_WARNING, 'Une demande a déjà été faite avec cet email');
                    return $this->redirectToRoute($request->get('_route'));
                }

                // Create a new request
                $resetRequest = new ResetRequest();
                $resetRequest->setTokenRequest($security->getResetPasswordToken($userFound));
                $resetRequest->setIdUsersite($userFound);
                $em->persist($resetRequest);
                $em->flush();

                if ($mailer->sendResetRequest($userFound->getEmail(), $resetRequest->getTokenRequest())) {
                    $this->addFlash(Constants::FLASH_SUCCESS, 'Un email a été envoyé à l\'adresse email indiquée');
                } else {
                    $this->addFlash(Constants::FLASH_WARNING, 'L\'email n\'a pas pu être envoyé');
                }

                return $this->redirectToRoute('login');
            }
        }

        return $this->render('security/reset.request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token)
    {
        $em = $this->getDoctrine()->getManager();
        $resetRequest = $em->getRepository('App:ResetRequest')->findOneBy([
            'tokenRequest' => $token,
        ]);

        if(null === $resetRequest) {
            $this->addFlash(Constants::FLASH_WARNING, 'Le jeton fourni est inconnu');
            return $this->redirectToRoute('login');
        }

        $user = $resetRequest->getIdUsersite();
        $form = $this->createForm(ResetPasswordType::class, $user);
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $password = $passwordEncoder->encodePassword($user, $form->get('password')->getData());
                $user->setPassword($password);
                $em->flush();
                $this->addFlash(Constants::FLASH_SUCCESS, 'Le mot de passe a été changé');

                return $this->redirectToRoute('login');
            }
        }

        return $this->render('security/reset.password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

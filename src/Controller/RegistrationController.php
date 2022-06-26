<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationFormUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationController extends AbstractController
{

    private $em;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }



    // ---------- DEBUT CRUD - CREATE - AJOUTER -------------//
    #[Route('/admin/membre/register', name: 'register_new')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles([$form->get("role")->getData()]);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('register_list');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    // ---------- FIN CRUD - CREATE - AJOUTER -------------//




    // ---------- DEBUT CRUD - READ - AFFICHER ----------------//
    #[Route('/admin/membre/list', name: 'register_list')]
    public function list() :Response{
    
        $membres = $this->em->getRepository(User::class)->findAll();
    
        return $this->render("registration/list.html.twig" , [
            "membres" => $membres
    
        ]);
    }
    // ---------- FIN CRUD - READ - AFFICHER ----------------//






    // ---------- DEBUT CRUD - UPDATE - MODIFIER -------------//
    #[Route('/admin/membre/update/{id}', name: 'register_update')]
        public function update($id, Request $request, UserPasswordHasherInterface $userPasswordHasher) :Response{
            $membreAModifier = $this->em->getRepository(User::class)->find($id);
            if($membreAModifier === null)  return $this->redirectToRoute('register_list');

            //symfony console make:form RegistrationFormUpdateType
            // user
            $form = $this->createForm(RegistrationFormUpdateType::class , $membreAModifier);

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){

                if($form->get('plainPassword')->getData() != ""){
                    // encode the plain password
                    $membreAModifier->setPassword(
                    $userPasswordHasher->hashPassword(
                            $membreAModifier,
                            $form->get('plainPassword')->getData()
                        )
                    );
                }
                if($form->get("role")->getData() != ""){
                    $membreAModifier->setRoles([$form->get("role")->getData()]);
                }


                $this->em->persist($membreAModifier);
                $this->em->flush();
                return $this->redirectToRoute('vehicule_list');
                // do anything else you need here, like send an email
            }


            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);

    }
    // ---------- FIN CRUD - UPDATE - MODIFIER -------------//





    // ---------- DEBUT CRUD - DELETE - SUPPRESION -------------//
    #[Route('/admin/membre/suppr/{id}', name: 'register_suppr')]
    public function suppr( $id ) : RedirectResponse{

        $membreASupprimer = $this->em->getRepository(User::class)->find($id);
        if( $membreASupprimer){
            $this->em->remove($membreASupprimer);
            $this->em->flush();
        }
        return $this->redirectToRoute('register_list');

    }
    // ---------- FIN CRUD - DELETE - SUPPRESION -------------//


}

<?php

namespace App\Controller ;

use DateTime;
use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController{

    /*
     * associer à une methode plusieurs routes en meme temps sur une methode + ParamConverter
    */

    // ---------- DEBUT CRUD - CREATE + UPDATE - AJOUTER et MODIFIER  ----------NOUVELLE METHODE---//
    #[Route("/admin/commande/new" , name:"commande_new")]
    #[Route("/admin/commande/update{id}" , name:"commande_update")]
    public function new(Request $request ,EntityManagerInterface $em , Commande $commande = null):Response{

        // si /admin/commande/new => $commande = null
        // si /admin/commande/update/{id} => $commande = $em->getRepository(Commande::class)->find($id); donc $comande = {}
        if($commande === null){
            $commande = new Commande();
            $commande->setDateHeureDepart( new DateTime())
                     ->setDateHeureFin(new DateTime());
        }

        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

        //recupere la date de debut et la date de fin
        // recupere le prix journalier qui vient du vehicule choisit 

        //multiplication = nbr de jour * prix journalier 

            $dt_debut = $form->get("date_heure_depart")->getData();
            $dt_fin = $form->get("date_heure_fin")->getData();
            $interval = $dt_debut->diff($dt_fin);
            $interval->format("%d");
            $nbJours = $interval->days ; 

            if($nbJours < 1){
                $this->addFlash("message" , "une reservation doit durer 24h au minimum");
                //return $this->redirectToRoute("commande_new" , $request->query->all());
            }
            $listevehiculeLoue = $em->getRepository(Commande::class)->listeVehiculeLoue($dt_debut ,$dt_fin );
            $vehicule = $form->get("vehicule")->getData();
            if(in_array( $vehicule->getId() , $listevehiculeLoue)){

                $listevehiculeDisponible = $em->getRepository(Vehicule::class)->findByVehiculeDisponibles($listevehiculeLoue );
                // $listevehiculeDisponible
                $this->addFlash("message" , "le véhicule demandé est déjà réservé");
                $this->addFlash("vehicules" , ["disponibles" => $listevehiculeDisponible] );
                //return $this->redirectToRoute("commande_new" , $request->query->all());
            }

            // dd($listevehiculeLoue , $listevehiculeDisponible); 

            if(!in_array( $vehicule->getId() , $listevehiculeLoue) && $nbJours >= 1){
                $prix_journalier = $vehicule->getPrixJournalier();

                $commande->setPrixTotal($nbJours * $prix_journalier);
                $em->persist($commande);
                $em->flush();
                return $this->redirectToRoute("commande_list");
                // regarder dans la base de données 
            }
           
        }

        return $this->render("commande/new.html.twig" , [
            "form" => $form->createView(),
            "id"   => $commande->getId()
        ]);
    }
    // ---------- FIN CRUD - CREATE - AJOUTER -------------//


    // ---------- DEBUT CRUD - CREATE - AFFICHER -------------//
    #[Route("/admin/commande/list" , name:"commande_list")]
    public function list(EntityManagerInterface $em):Response{
        $commandes = $em->getRepository(Commande::class)->findAll();

    return $this->render("commande/list.html.twig" , compact("commandes"));
    //["commandes" => $commandes]

    }
    // ---------- FIN CRUD - CREATE - AFFICHER -------------//




    // ---------- DEBUT CRUD - UPDATE - MODIFIER ancien -------------//

    // ---------- FIN CRUD - UPDATE - MODIFIER -------------//




    // ---------- DEBUT CRUD - UPDATE - MODIFIER ---------NOUVELLE METHODE----//
    #[Route("/admin/commande/suppr{id}" , name:"commande_suppr")]
    public function delete(Commande $commandeASupprimer , EntityManagerInterface $em){

        if($commandeASupprimer !== null){
            $em->remove($commandeASupprimer);
            $em->flush();
        }
        return $this->redirectToRoute("commande_list");
    }
    // ---------- FIN CRUD - UPDATE - MODIFIER ---------NOUVELLE METHODE----//

        
    





    
}
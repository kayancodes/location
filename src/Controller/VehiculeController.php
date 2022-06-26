<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Services\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[Route('/admin/vehicule')]
class VehiculeController extends AbstractController{







    // ---------- DEBUT CRUD - CREATE - AJOUTER -------------//
    #[Route('/new', name: 'vehicule_new')]
    public function new(Request $request) : Response{

        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            // recuperer le fichier 
            // le nommer 
            // $file = $request->files->get("vehicule")["photo"];
            $file = $form["photo"]->getData();


            // ------invocation de code -----//
            $this->imgService->moveImage($file , $vehicule );
            // ------invocation de code -----//


            // dans le dossier public creer dossier upload
            $this->em->persist($vehicule);
            $this->em->flush();
            return $this->redirectToRoute("vehicule_list");

        }

        return $this->render('vehicule/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    // ---------- FIN CRUD - CREATE - AJOUTER -----------------//







    // ---------- DEBUT CRUD - READ - AFFICHER ----------------//
    private $em;
    private $imgService;

    public function __construct(EntityManagerInterface $em, ImageService $imgService){

        $this->em = $em;
        $this->imgService = $imgService;

    }


    #[Route('/list', name: 'vehicule_list')]
    public function list():Response{

           $vehicules = $this->em->getRepository(Vehicule::class)->findAll();

            return $this->render( "vehicule/list.html.twig", ["vehicules" => $vehicules]);

    }
    
    // ---------- DEBUT CRUD - READ - AFFICHER ----------------//






    // ---------- DEBUT CRUD - DELETE - SUPPRIMER ----------------//
    #[Route('/supp/{id}', name: 'vehicule_suppr')]
        public function suppr($id) :RedirectResponse{

        $vehiculeASupprimer = $this->em->getRepository(vehicule::class)->find($id);

        if($vehiculeASupprimer){

             // ------invocation de code -----//
            $this->imgService->deleteImage($vehiculeASupprimer);
             // ------invocation de code -----//

            $this->em->remove($vehiculeASupprimer);
            $this->em->flush();
        }

        return $this->redirectToRoute("vehicule_list");
    }
    // ---------- FIN CRUD - DELETE - SUPPRIMER ----------------//








    // ---------- DEBUT CRUD - UPLOAD - MODIFIER >>>> SEMBLABLE A "AJOUTER"----------------//
    #[Route('/update/{id}', name: 'vehicule_update')]
    public function update(Request $request, $id) :Response{
        $vehicule = $this->em->getRepository(Vehicule::class)->find($id);

        if($vehicule === null) return $this->redirectToRoute("vehicule_list");
        

        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            // recuperer le fichier 
            // le nommer 
            // $file = $request->files->get("vehicule")["photo"];
            $file = $form["photo"]->getData();

            if($file){
                 // ------invocation de code -----//
                $this->imgService->updateImage($file , $vehicule );
                // ------invocation de code -----//
            }
            


            // dans le dossier public creer dossier upload
            $this->em->persist($vehicule);
            $this->em->flush();
            return $this->redirectToRoute("vehicule_list");

        }

        return $this->render('vehicule/new.html.twig', [
            'form' => $form->createView()
        ]);

    }


    // ---------- FIN CRUD - UPLOAD - MODIFIER ----------------//


}


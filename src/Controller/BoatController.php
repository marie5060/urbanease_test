<?php

namespace App\Controller;

use App\Entity\Boat;
use App\Form\BoatType;
use App\Service\MapManager;
use App\Repository\BoatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/boat")
 */
class BoatController extends AbstractController
{

    /**
     * Move the boat to coord x,y
     * @Route("/move/{x}/{y}", name="moveBoat", requirements={"x"="\d+", "y"="\d+"}))
     */
    public function moveBoat(int $x, int $y, BoatRepository $boatRepository, EntityManagerInterface $em) :Response
    {
        $boat = $boatRepository->findOneBy([]);
        $boat->setCoordX($x);
        $boat->setCoordY($y);

        $em->flush();

        return $this->redirectToRoute('map');
    }


     // Move the boat to N , W, S or E and stop if out the map
    /**
     * Move the boat to coord x,y
     * @Route("/direction/{direction}", name = "moveDirection", requirements= {"direction" = "[N => \w, S => \w, W => \w, E => \w]"})
     */

    public function moveDirection(string $direction, BoatRepository $boatRepository, EntityManagerInterface $em, MapManager $mapManager): Response
    {
       
        $boat = $boatRepository->findOneBy([]);
        $boatX = $boat->getCoordX();
        $boatY =$boat->getCoordY();

        if ($direction === "E")
        {
          $boat->setCoordX($boatX +1);

        } else if ($direction === "W")
        {
            $boat->setCoordX( $boatX -1);

        } else if ($direction === "S")
        {
            $boat->setCoordY($boatY +1);

        } else if ($direction === "N")
        {
            $boat->setCoordY( $boatY-1);
        }
        
        
        //test if boat's coord and tile's coord are the same 
        $tile = $mapManager->tileExists($boat->getCoordX(),$boat->getcoordY());
        //test if boat's coord and tile with treasure coord are the same
        $treasure = $mapManager->checkTreasure($boat);

        //if out of the map
        if (!$tile){
             $this->addFlash('warning', 'This direction is impossible');
            return $this->redirectToRoute('map');
        //if not    
        } else {
            //and if on tresureIsland
            if($treasure){
            $this->addFlash('info','Congrats ! You find the hidden treasure');
            }
            $em->persist($boat);
            $em->flush();
        }

        $em->flush();
        return $this->redirectToRoute('map');
    }


    /**
     * @Route("/", name="boat_index", methods="GET")
     */
    public function index(BoatRepository $boatRepository): Response
    {
        return $this->render('boat/index.html.twig', ['boats' => $boatRepository->findAll()]);
    }

    /**
     * @Route("/new", name="boat_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $boat = new Boat();
        $form = $this->createForm(BoatType::class, $boat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($boat);
            $em->flush();

            return $this->redirectToRoute('boat_index');
        }

        return $this->render('boat/new.html.twig', [
            'boat' => $boat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="boat_show", methods="GET")
     */
    public function show(Boat $boat): Response
    {
        return $this->render('boat/show.html.twig', ['boat' => $boat]);
    }

    /**
     * @Route("/{id}/edit", name="boat_edit", methods="GET|POST")
     */
    public function edit(Request $request, Boat $boat): Response
    {
        $form = $this->createForm(BoatType::class, $boat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('boat_index', ['id' => $boat->getId()]);
        }

        return $this->render('boat/edit.html.twig', [
            'boat' => $boat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="boat_delete", methods="DELETE")
     */
    public function delete(Request $request, Boat $boat): Response
    {
        if ($this->isCsrfTokenValid('delete' . $boat->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($boat);
            $em->flush();
        }

        return $this->redirectToRoute('boat_index');
    }
}
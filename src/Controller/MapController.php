<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tile;
use App\Repository\BoatRepository;
use App\Repository\TileRepository;

class MapController extends AbstractController
{
    /**
     * @Route("/map", name="map")
     */
    public function displayMap(BoatRepository $boatRepository, TileRepository $tileRepository) :Response
    {
        $em = $this->getDoctrine()->getManager();
        $tiles = $em->getRepository(Tile::class)->findAll();

        foreach ($tiles as $tile) {
            $map[$tile->getCoordX()][$tile->getCoordY()] = $tile;
        }

           // get coord of boat position
           $boat = $boatRepository->findOneBy([]);
           $boatX=$boat->getCoordX();
           $boatY=$boat->getCoordY();
   
           // get tile where is the boat
           $tile = $tileRepository->findOneBy(['coordX'=>$boatX,'coordY'=> $boatY]);

        return $this->render('map/index.html.twig', [
            'map'  => $map ?? [],
            'boat' => $boat,
            'tile' => $tile,
        ]);
    }


    //method to start a new game and reset coords boat, and put treasure on a new tile
    /**
    * @Route("/start", name="start")
    */
    public function start(BoatRepository $boatRepository, EntityManagerInterface $em, TileRepository $tileRepository, MapManager $mapManager)
    {
        // set coord of boat 
        $boat = $boatRepository->findOneBy([]);
        $boat->setCoordX(0);
        $boat->setCoordY(0);
        $em->persist($boat);

        //pull all tiles on hasTreasure(false) 
        $tiles = $tileRepository->findAll();
        foreach ($tiles as $tile) {
        $tile->setHasTreasure(false);
        $em->persist($tile);
        }
        //and put a new one on a randomIsland
        $treasureIsland = $mapManager->getRandomIsland();
        $treasureIsland->setHasTreasure(true);
        $em->persist($treasureIsland);
       
        $em->flush();

        return $this->redirectToRoute('map');

    }

}
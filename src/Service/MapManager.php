<?php
namespace App\Service;
use App\Entity\Tile;
use app\Entity\Boat;
use App\Repository\TileRepository;

class MapManager 
{
    private TileRepository $tileRepository;

    public function __construct(TileRepository $tileRepository)
    {
        $this->tileRepository = $tileRepository;
    }
    
    public function tileExists(int $x,int $y) : bool
    {
        //get the coord x and y for one tile
        $tile = $this->tileRepository->findOneBy(['coordX'=>$x,'coordY'=>$y]);

        //check if Tile's coord exist and return a bool
        return $tile ? true : false ;
    }
    
    public function getRandomIsland() : Tile
    {
        //get all tiles with island type
        $tiles = $this->tileRepository->findByType(['type'=>'island']);
        //get one random islands from the array
        $randomIsland = $tiles[array_rand($tiles,1)];
        //return randomIsland
        return $randomIsland;
    }

        public function checkTreasure( Boat $boat) 
    {
        //get the coord x and y for treasureTile
        $treasureTile = $this->tileRepository->findOneBy(['hasTreasure'=>true]);

        //check if treasureTile's coord and boatCoord are the same and return bool
        if ($boat->getCoordX() == $treasureTile->getCoordX() and $boat->getCoordY() == $treasureTile->getCoordY() ){
            return true ;
        }
        return false;
    }
}
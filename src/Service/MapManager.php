<?php
namespace App\Service;
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
    
    public function getRandomIsland(int $x,int $y) : Tile
    {
        //get all tiles with island type
        $tiles = $this->tileRepository->findByType(['type'=>'island']);
        //get one random islands from the array
        $randomIsland = array_rand($titles,1);
        //return randomIsland
        return $randomIsland;
    }

}
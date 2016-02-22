<?php
/**
 * Created by PhpStorm.
 * User: Lucas
 * Date: 21/02/2016
 * Time: 19:36
 */

namespace LotrBundle\Service;


class MapGenerator
{
    public $color = [];
    public $status = [];
    public $oldCoordX;
    public $oldCoordY;
    public $image;

    public function generate($mapType = 0, $trip = null, $place = null)
    {
        header ("Content-type: image/png");

        // Set the map format
        $this->image = $this->setMap($mapType);

        // Set var color
        $this->color = [
            'aragorn' => imagecolorallocate($this->image, 0x68, 0x0E, 0x0E),
            'boromir' => imagecolorallocate($this->image, 0xED, 0xE6, 0x00),
            'frodon' => imagecolorallocate($this->image, 0xDD, 0x00, 0x2A),
            'gandalf' => imagecolorallocate($this->image, 0x8F, 0x00, 0xDB),
            'gimli' => imagecolorallocate($this->image, 0x00, 0x10, 0xDD),
            'legolas' => imagecolorallocate($this->image, 0xFC, 0x6B, 0xBC),
            'merry' => imagecolorallocate($this->image, 0x49, 0xD3, 0x00),
            'pippin' => imagecolorallocate($this->image, 0x00, 0xAE, 0xD6),
            'sam' => imagecolorallocate($this->image, 0xDD, 0x78, 0x00),
            'place' => imagecolorallocate($this->image, 0xFF, 0xFF, 0xFF),
        ];
        $this->status = [
            2 => imagecolorallocate($this->image, 0xED, 0xE6, 0x00),
            3 => imagecolorallocate($this->image, 0x00, 0x00, 0x00),
            4 => imagecolorallocate($this->image, 0x49, 0xD3, 0x00),
            5 => imagecolorallocate($this->image, 0x8F, 0x00, 0xDB),
        ];
        $this->oldCoordX = null;
        $this->oldCoordY = null;

        // Print the place
        if($place)
        {
            $this->printPlace($this->image, $place, $trip, $this->color);
        }

        // Print the trip
        if($trip)
        {
            $this->printTrip($this->image, $trip, $this->color, $this->status, $this->oldCoordX, $this->oldCoordY);
        }

        // Send png to the client
        imagepng($this->image);
        //die();
    }

    private function setMap($mapType)
    {
        switch ($mapType)
        {
            case 0:
                $image = imagecreatefrompng(__DIR__ . '/../Resources/maps/mapNormale.png');
                break;
            case 1:
                $image = imagecreatefrompng(__DIR__ . '/../Resources/maps/mapLegende.png');
                break;
            case 2:
                $image = imagecreatefrompng(__DIR__ . '/../Resources/maps/mapNumeros.png');
                break;
            case 3:
                $image = imagecreatefrompng(__DIR__ . '/../Resources/maps/mapGrid.png');
                break;
            case 4:
                $image = imagecreatefrompng(__DIR__ . '/../Resources/maps/mapLegendeEtNumeros.png');
                break;
            case 5:
                $image = imagecreatefrompng(__DIR__ . '/../Resources/maps/mapLegendeEtGrid.png');
                break;
            default:
                $image = imagecreatefrompng(__DIR__ . '/../Resources/maps/mapNormale.png');
        }
        return $image;
    }

    private function printTrip($image, $trip, $color, $status, $oldCoordX, $oldCoordY)
    {
        foreach ($trip as $item)
        {
            // Print Status if different of ok
            if ($item->getStatus()->getId() && $item->getStatus()->getId() != 1)
            {
                ImageFilledEllipse($image, $item->getCoordx() * 10, $item->getCoordy() * 10, 14, 14, $status[$item->getStatus()->getId()]);
            }

            // Print position
            ImageFilledEllipse($image, $item->getCoordx() * 10, $item->getCoordy() * 10, 7, 7, $color[$item->getCharacter()->getSlug()]);

            // Print the way if different of -1/-1
            if ($oldCoordX && $oldCoordX != -1 && $item->getCoordx() != -1)
            {

                ImageLine($image, $item->getCoordx() * 10, $item->getCoordy() * 10, $oldCoordX * 10, $oldCoordY * 10, $color[$item->getCharacter()->getSlug()]);

            }
            $oldCoordX = $item->getCoordx();
            $oldCoordY = $item->getCoordy();
        }
    }

    private function printPlace($image, $place, $trip, $color)
    {
        if($trip)
        {
            $radius = 24;
        }
        else
        {
            $radius = 8;
        }
        foreach ($place as $item)
        {
            ImageFilledEllipse($image, $item->getCoordx() * 10, $item->getCoordy() * 10, $radius, $radius, $color['place']);
            if(count($place) == 1)
            {
                Imagestring($image, 5, ($item->getCoordx() - 2) * 10, ($item->getCoordy() - 3) * 10, $item->getName(), $color['place']);
            }
        }
    }
}
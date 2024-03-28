<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Builder\BuilderInterface;

class QrCodeService
{
    protected $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function qrcode($query)
    {


        $objDateTime = new \DateTime('NOW');
        $dateString = $objDateTime->format('d-m-Y H:i:s');

        $path = dirname(__DIR__, 2).'/public/assets/img/';

        // set qrcode
        $result = $this->builder
            ->data($query)
            ->encoding(new Encoding('UTF-8'))
            ->size(400)
            ->margin(10)
            ->labelText($dateString)
            ->labelMargin(new Margin(15, 5, 5, 5))

            ->backgroundColor(new Color(173, 216, 230))
            ->build()
        ;

        //generate name
        $namePng = 'codeQr.png';

        //Save img png
        $result->saveToFile($path.'qrCode/'.$namePng);

        return $result->getDataUri();
    }
}

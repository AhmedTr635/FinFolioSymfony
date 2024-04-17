<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use HWI\Bundle\OAuthBundle\HWIOAuthBundle;


class Kernel extends BaseKernel
{
    use MicroKernelTrait;



}

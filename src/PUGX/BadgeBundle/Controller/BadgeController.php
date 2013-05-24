<?php

namespace PUGX\BadgeBundle\Controller;

use PUGX\BadgeBundle\Service\ImageCreator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BadgeController extends Controller
{

    public function downloadsAction($vendor, $repository, $type = 'total')
    {
        $repository = $vendor . '/' . $repository;
        $filename = sprintf('%s.png', $type);
        $statType = 'get' . ucfirst($type);
        // get the download statistic from packagist and then make it readable
        $downloads = $this->get('badger')->getPackageDownloads($repository);
        $downloadsText = ImageCreator::ERROR_TEXT_CLIENT_EXCEPTION;
        $httpCode = 500;

        if ($downloads && $downloads->{$statType}() >= 0) {
            $downloadsText = $this->get('image_creator')->transformNumberToReadableFormat($downloads->{$statType}());
            $httpCode = 200;
        }
        // image creation
        $image = $this->get('image_creator')->createImageDownloads($downloadsText);
        //generating the streamed response
        $response = new StreamedResponse(null, $httpCode);
        $response->setCallback(function () use ($image) {
            imagepng($image);
        });
        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Disposition', 'inline; filename="'.$filename.'"');
        $response->send();
        imagedestroy($image);
        return $response;
    }
}
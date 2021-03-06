<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Image\Factory;

use Guzzle\Http\ClientInterface;
use PUGX\Badge\Image\Image;
use PUGX\Badge\Image\ImageFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ShieldIOImageCreator, responsible to create an Image Object
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class ShieldIOFactory implements ImageFactoryInterface
{
    /**
     * @var array $definedColors
     */
    private static $definedColors = array(
        self::DOWNLOADS => 'blue',
        self::STABLE    => '28a3df',
        self::UNSTABLE  => 'e68718',
        self::ERROR     => 'red',
        self::LICENSE   => '428F7E'
    );

    /**
     * @var ClientInterface $httpClient
     */
    private $httpClient;

    /**
     * @var UrlGeneratorInterface $routeGenerator
     */
    private $routeGenerator;

    /**
     * @var string $routeName
     */
    private $routeName;

    /**
     * @param ClientInterface       $httpClient
     * @param UrlGeneratorInterface $routeGenerator
     * @param string                $routeName
     */
    public function __construct(ClientInterface       $httpClient,
                                UrlGeneratorInterface $routeGenerator,
                                                      $routeName      = 'pugx_badge_shieldio')
    {
        $this->httpClient     = $httpClient;
        $this->routeGenerator = $routeGenerator;
        $this->routeName      = $routeName;
    }

    /**
     * Create the 'downloads' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return \PUGX\Badge\Image\Image
     */
    public function createDownloadsImage($value)
    {
        $response = $this->fetchResponse(self::DOWNLOADS, $value, self::$definedColors[self::DOWNLOADS]);

        return Image::createFromString($response->getBody(true));
    }

    /**
     * Create the 'stable:no release' image with the standard Font and stable image template.
     *
     * @param string $value
     *
     * @return \PUGX\Badge\Image\Image
     */
    public function createStableNoImage($value)
    {
        $response = $this->fetchResponse(self::STABLE, $value, self::$definedColors[self::STABLE]);

        return Image::createFromString($response->getBody(true));
    }

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createStableImage($value)
    {
        $response = $this->fetchResponse(self::STABLE, $value, self::$definedColors[self::STABLE]);

        return Image::createFromString($response->getBody(true));
    }

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createUnstableImage($value = '@dev')
    {
        $response = $this->fetchResponse(self::UNSTABLE, $value, self::$definedColors[self::UNSTABLE]);

        return Image::createFromString($response->getBody(true));
    }

    /**
     * Create the 'error' image
     *
     * @param string $value
     *
     * @return \PUGX\Badge\Image\Image
     */
    public function createErrorImage($value)
    {
        $response = $this->fetchResponse(self::ERROR, $value, self::$definedColors[self::ERROR]);

        return Image::createFromString($response->getBody(true));
    }

    /**
     * Create a 'license' Image
     *
     * @param string $value
     *
     * @return \PUGX\Badge\Image\Image
     */
    public function createLicenseImage($value)
    {
        $response = $this->fetchResponse(self::LICENSE, $value, self::$definedColors[self::LICENSE]);

        return Image::createFromString($response->getBody(true));
    }

    /**
     * @param $vendor
     * @param $value
     * @param $color
     *
     * @return string
     */
    private function generateURI($vendor, $value, $color)
    {
        $value = str_replace('-', '--', $value);
        $value = str_replace(' ', '_', $value);
        $value = str_replace(' ', '_', $value);

        $parameters = array('vendor' => $vendor, 'color' => $color, 'value' => $value, 'extension' => 'svg');

        return $this->routeGenerator->generate($this->routeName, $parameters, true);
    }

    /**
     * @param $vendor
     * @param $value
     * @param $color
     *
     * @return array|\Guzzle\Http\Message\Response
     */
    private function fetchResponse($vendor, $value, $color)
    {
        $request  = $this->httpClient->get($this->generateURI($vendor, $value, $color));
        $response = $this->httpClient->send($request);

        return $response;
    }
}

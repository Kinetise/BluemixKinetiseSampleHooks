<?php

namespace KinetiseSkeleton\Controller;

use Silex\Application;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractController
{
    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->application['orm.em'];
    }

    /**
     * @return SerializerInterface
     */
    protected function getSerializer()
    {
        return $this->application['jms.serializer'];
    }

    /**
     * @return UrlGeneratorInterface
     */
    public function getUrlGenerator()
    {
        return $this->application['url_generator'];
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->application['twig'];
    }
}
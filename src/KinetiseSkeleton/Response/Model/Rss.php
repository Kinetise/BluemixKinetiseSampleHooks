<?php

namespace KinetiseSkeleton\Response\Model;

use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation AS JMS;

/**
 * Class Rss
 * @package Kinetise\Response\Model
 *
 * @JMS\XmlRoot("rss")
 * @JMS\XmlNamespace(uri="http://kinetise.com", prefix="k")
 */
class Rss
{
    /**
     * @var Channel
     */
    private $channel;

    public function __construct(Collection $data = null)
    {
        $this->channel = new Channel($data);
    }
}

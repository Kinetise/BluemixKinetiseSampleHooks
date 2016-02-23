<?php

namespace KinetiseSkeleton\Response\Model;

use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Channel
 * @package Kinetise\Response\Model
 */
class Channel
{
    /**
     * @JMS\XmlList(inline = true, entry = "item")
     */
    private $items;

    public function __construct(Collection $data = null)
    {
        $this->items = $data;
    }
}

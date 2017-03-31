<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Officeequipments
 *
 * @ORM\Table(name="officeequipments")
 * @ORM\Entity
 */
class Officeequipments
{
    /**
     * @var string
     *
     * @ORM\Column(name="item_type", type="string", length=255, nullable=true)
     */
    private $itemType;

    /**
     * @var integer
     *
     * @ORM\Column(name="officeequipments_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="officeequipments_officeequipments_id_seq", allocationSize=1, initialValue=1)
     */
    private $officeequipmentsId;



    /**
     * Set itemType
     *
     * @param string $itemType
     *
     * @return Officeequipments
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * Get itemType
     *
     * @return string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * Get officeequipmentsId
     *
     * @return integer
     */
    public function getOfficeequipmentsId()
    {
        return $this->officeequipmentsId;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Officesupplies
 *
 * @ORM\Table(name="officesupplies")
 * @ORM\Entity
 */
class Officesupplies
{
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchased", type="date", nullable=true)
     */
    private $purchased;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="officesupplies_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="officesupplies_officesupplies_id_seq", allocationSize=1, initialValue=1)
     */
    private $officesuppliesId;



    /**
     * Set type
     *
     * @param string $type
     *
     * @return Officesupplies
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Officesupplies
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set purchased
     *
     * @param \DateTime $purchased
     *
     * @return Officesupplies
     */
    public function setPurchased($purchased)
    {
        $this->purchased = $purchased;

        return $this;
    }

    /**
     * Get purchased
     *
     * @return \DateTime
     */
    public function getPurchased()
    {
        return $this->purchased;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Officesupplies
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get officesuppliesId
     *
     * @return integer
     */
    public function getOfficesuppliesId()
    {
        return $this->officesuppliesId;
    }
}

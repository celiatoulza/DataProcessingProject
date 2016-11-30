<?php

namespace DataProcessingProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdvertActivity
 *
 * @ORM\Table(name="advert_activity")
 * @ORM\Entity(repositoryClass="DataProcessingProjectBundle\Repository\AdvertActivityRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AdvertActivity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="text", nullable=true)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="more_description", type="text", nullable=true)
     */
    private $moreDescription;

    /**
     * @ORM\ManyToOne(targetEntity="DataProcessingProjectBundle\Entity\Advert")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $advert;

    /**
     * @ORM\ManyToOne(targetEntity="DataProcessingProjectBundle\Entity\Activity")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $activity;





    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return AdvertActivity
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return AdvertActivity
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set moreDescritpion
     *
     * @param string $moreDescritpion
     *
     * @return AdvertActivity
     */
    public function setMoreDescription($moreDescription)
    {
        $this->moreDescription = $moreDescription;

        return $this;
    }

    /**
     * Get moreDescritpion
     *
     * @return string
     */
    public function getMoreDescription()
    {
        return $this->moreDescription;
    }


    public function getActivity(){

        return $this->activity;
    }

    public function setActivity( $activity ){

        $this->activity = $activity;
        return $this;
    }

    public function getAdvert(){
        
        return $this->advert;
    }

    public function setAdvert( $advert ){

        $this->advert = $advert;
        return $this;
    }

    
}

<?php

namespace DataProcessingProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application
 *
 * @ORM\Table(name="application")
 * @ORM\Entity(repositoryClass="DataProcessingProjectBundle\Repository\ApplicationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Application
{

    ////////////////
    // ATTRIBUTES //
    ////////////////

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="applicationDate", type="datetime")
     */
    private $applicationDate;

    /**
     * @ORM\ManyToOne(targetEntity="DataProcessingProjectBundle\Entity\Advert", inversedBy="applications")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $advert;

    /**
     * @var \Integer
     *
     * @ORM\Column(name="nbPeople", type="integer", nullable=false)
     */
    private $nbPeople;

    /**
     * @var \Integer
     *
     * @ORM\Column(name="choiceNumber", type="integer", nullable=true)
     */
    private $choiceNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arrivalDate", type="datetime")
     */
    private $arrivalDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="departureDate", type="datetime")
     */
    private $departureDate;


    /////////////////
    // CONSTRUCTOR //
    /////////////////

    public function __construct(){
        $this->date = new \Datetime();
    }


    ///////////////////// 
    // GETTERS/SETTERS //
    /////////////////////


    public function getAdvertId(){
        return $this->getAdvert()->getId();
    }
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Application
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Application
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set applicationDate
     *
     * @param \DateTime $applicationDate
     *
     * @return Application
     */
    public function setApplicationDate($applicationDate)
    {
        $this->applicationDate = $applicationDate;

        return $this;
    }

    /**
     * Get applicationDate
     *
     * @return \DateTime
     */
    public function getApplicationDate()
    {
        return $this->applicationDate;
    }


    /**
     * Set advert
     *
     * @param \DataProcessingProjectBundle\Entity\Advert $advert
     *
     * @return Application
     */
    public function setAdvert(\DataProcessingProjectBundle\Entity\Advert $advert)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \DataProcessingProjectBundle\Entity\Advert
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Get nbPeople
     *
     * @return \Integer
     */
    public function getNbPeople(){
        return $this->nbPeople;
    }

    /**
     * Set nbPeople
     *
     * @param \Integer $nbPeople
     *
     * @return Application
     */
    public function setNbPeople( $nbPeople ){
        $this->nbPeople = $nbPeople;
    }

    /**
     * Get departureDate
     *
     * @return \DateTime
     */
    public function getDepartureDate(){
        return $this->departureDate;
    }

    /**
     * Set departureDate
     *
     * @param \DateTime $departureDate
     *
     * @return Application
     */
    public function setDepartureDate( $departureDate ){
        $this->departureDate = $departureDate;
    }

    /**
     * Get arrivalDate
     *
     * @return \DateTime
     */
    public function getArrivalDate(){
        return $this->arrivalDate;
    }

    /**
     * Set arrivalDate
     *
     * @param \DateTime $arrivalDate
     *
     * @return Application
     */
    public function setArrivalDate( $arrivalDate ){
        $this->arrivalDate = $arrivalDate;
    }

    /**
     * Get choiceNumber
     *
     * @return \Integer
     */
    public function getChoiceNumber(){
        return $this->choiceNumber;
    }

    /**
     * Set choiceNumber
     *
     * @param \Integer $choiceNumber
     *
     * @return Application
     */
    public function setChoiceNumber( $choiceNumber ){
        $this->choiceNumber = $choiceNumber;
    }


    /////////////
    // METHODS //
    /////////////

    /**
     * @ORM\PrePersist
     */
    public function increase(){

        $this->getAdvert()->increaseNbApplications();
    }

    /**
     * @ORM\PreRemove
     */
    public function decrease(){

        $this->getAdvert()->decreaseNbApplications();
    }
}

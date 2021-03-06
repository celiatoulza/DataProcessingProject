<?php

namespace DataProcessingProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Advert
 *
 * @ORM\Table(name="advert")
 * @ORM\Entity(repositoryClass="DataProcessingProjectBundle\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="title", message="Il existe déjà une annonce avec ce titre.")
 */
class Advert
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     * @Assert\Length(min=3)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\Length(min=2)
     */
    private $author;

    /**
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
    */
    protected $content;

    /**
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;

    /**
     * @ORM\OneToOne(targetEntity="DataProcessingProjectBundle\Entity\Image", cascade={"persist", "remove"})
     *@Assert\Valid()
    */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="DataProcessingProjectBundle\Entity\Category", cascade={"persist"})
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="DataProcessingProjectBundle\Entity\Application", mappedBy="advert")
     */
    private $applications;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="nbApplications", type="integer")
     */
    private $nbApplications;



    /////////////////
    // CONSTRUCTOR //
    /////////////////

    public function __construct(){

        $this->date = new \DateTime();
        $this->categories = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->nbApplications = 0;
    }



    /////////////
    // METHODS //
    /////////////

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
     * Set title
     *
     * @param string $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Advert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Advert
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }


    /**
     * Set image
     *
     * @param \DataProcessingProjectBundle\Entity\Image $image
     *
     * @return Advert
     */
    public function setImage(\DataProcessingProjectBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \DataProcessingProjectBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }


    public function addCategory( Category $category ){

        $this->categories[]= $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \DataProcessingProjectBundle\Entity\Category $category
     */
    public function removeCategory(\DataProcessingProjectBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    public function getCategories(){

        return $this->categories;
    }

     /**
     * Add application
     *
     * @param \DataProcessingProjectBundle\Entity\Application $application
     *
     * @return Advert
     */
    public function addApplication(\DataProcessingProjectBundle\Entity\Application $application)
    {
        $this->applications[] = $application;

        // on lie aussi l'annonce à la candidature parce que maintenant c'est bidirectionnel
        $application->setAdvert( $this );

        return $this;
    }

    /**
     * Remove application
     *
     * @param \DataProcessingProjectBundle\Entity\Application $application
     */
    public function removeApplication(\DataProcessingProjectBundle\Entity\Application $application)
    {
        $this->applications->removeElement($application);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Advert
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     *@ORM\PreUpdate
     */
    public function updateDate(){

        $this->setUpdatedAt( new \DateTime() );
    }

    /**
     * Set nbApplications
     *
     * @param integer $nbApplications
     *
     * @return Advert
     */
    public function setNbApplications($nbApplications)
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    /**
     * Get nbApplications
     *
     * @return integer
     */
    public function getNbApplications()
    {
        return $this->nbApplications;
    }

    public function increaseNbApplications(){

        $this->nbApplications++;
    }

    public function decreaseNbApplications(){

        $this->nbApplications--;
    }
}


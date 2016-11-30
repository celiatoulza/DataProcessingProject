<?php

namespace DataProcessingProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="activity")
 * @ORM\Entity(repositoryClass="DataProcessingProjectBundle\Repository\ActivityRepository")
 */
class Activity
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="DataProcessingProjectBundle\Entity\Image", cascade={"persist"})
     *
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="DataProcessingProjectBundle\Entity\ActivityCategory", cascade={"persist"})
     * @ORM\JoinColumn(name="categories", referencedColumnName="id", nullable=true)
     */
    private $categories;

    /**
     * @var int
     *
     * @ORM\Column(name="average_price", type="float", nullable=true)
     */
    private $averagePrice;


    

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
     * Set name
     *
     * @param string $name
     *
     * @return Activity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Activity
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
     * Set image
     *
     * @param \DataProcessingProjectBundle\Entity\Image $image
     *
     * @return Activity
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add category
     *
     * @param \DataProcessingProjectBundle\Entity\ActivityCategory $category
     *
     * @return Activity
     */
    public function addCategory(\DataProcessingProjectBundle\Entity\ActivityCategory $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \DataProcessingProjectBundle\Entity\ActivityCategory $category
     */
    public function removeCategory(\DataProcessingProjectBundle\Entity\ActivityCategory $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set averagePrice
     *
     * @param integer $averagePrice
     *
     * @return Activity
     */
    public function setAveragePrice($averagePrice)
    {
        $this->averagePrice = $averagePrice;

        return $this;
    }

    /**
     * Get averagePrice
     *
     * @return float
     */
    public function getAveragePrice()
    {
        return $this->averagePrice;
    }
}

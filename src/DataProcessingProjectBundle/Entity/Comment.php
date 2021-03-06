<?php

namespace DataProcessingProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="DataProcessingProjectBundle\Repository\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", nullable=false, onDelete="cascade")
     */
    private $author;


    /**
     * @ORM\ManyToOne(targetEntity="DataProcessingProjectBundle\Entity\Advert")
     * @ORM\JoinColumn(name="advert", nullable=false, onDelete="cascade")
     */
    private $advert;

    /////////////////
    // CONSTRUCTOR //
    /////////////////


    public function __construct( $author, $advert ){
        $this->date = new \Datetime();
        $this->author = $author;
        $this->advert = $advert;
    }


    ///////////////////// 
    // GETTERS/SETTERS //
    /////////////////////


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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Comment
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
     * @param \UserBundle\Entity\User $author
     *
     * @return Comment
     */
    public function setAuthor(\UserBundle\Entity\User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set advert
     *
     * @param \DataProcessingProjectBundle\Entity\Advert $advert
     *
     * @return Comment
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
}

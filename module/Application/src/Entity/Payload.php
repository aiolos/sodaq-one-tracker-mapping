<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="payload")
 */
class Payload
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="content", type="text")
     */
    protected $content;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    const STATUS_NEW = 1;
    const STATUS_PROCESSED = 2;

    /**
     * @PrePersist
     */
    public function onPrePersistSetRegistrationDate()
    {
        $this->dateCreated = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Show a formatted version of the status
     * @return string
     */
    public function getFormattedStatus()
    {
        $statuses = [
            self::STATUS_NEW => 'New',
            self::STATUS_PROCESSED => 'Processed',
        ];
        return $statuses[$this->status];
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'content' => $this->getContent(),
            'date' => $this->getDateCreated(),
        ];
    }
}

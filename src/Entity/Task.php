<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TaskRepository;
use JsonSerializable;

/**
* @ORM\Entity(repositoryClass=TaskRepository::class)
* @ORM\Table(name="tasks")
*/
class Task implements JsonSerializable
{

    private const TO_STRING_PREFIX = '%s - %s - %s';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tasks")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private User $owner;

    /**
     * @ORM\Column(type="string", length=2500)
     */
    private string $summary;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected DateTime $createdAt;

    public function __construct(User $user, string $summary)
    {
        $this->owner = $user;
        $this->summary = $summary;
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return sprintf(
            self::TO_STRING_PREFIX,
            $this->owner->getUserIdentifier(),
            $this->getSummary(),
            $this->createdAt->format('Y-m-d H:i:s')
        );
    }

    public function jsonSerialize(): array
    {
        return [
           'id'         => $this->id,
           'owner'      => $this->owner->getUserIdentifier(),
           'summary'    => $this->getSummary(),
           'createdAt'  => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
}

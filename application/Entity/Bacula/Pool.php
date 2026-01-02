<?php

namespace App\Entity\Bacula;

use App\Entity\Bacula\Repository\PoolRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PoolRepository::class)]
#[ORM\Table(name: 'Pool')]
class Pool
{
    #[ORM\Id]
    #[ORM\Column(name: 'PoolId', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'Name', type: 'string')]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'pool', targetEntity: Volume::class)]
    /**
     * @var Collection
     */
    private Collection $volumes;

    #[ORM\Column(type: "integer")]
    private int $numvols;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TableroRepository")
 */
class Tablero
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $estado = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $turno;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $modo_juego;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstado(): ?array
    {
        return $this->estado;
    }

    public function setEstado(array $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getTurno(): ?int
    {
        return $this->turno;
    }

    public function setTurno(int $turno): self
    {
        $this->turno = $turno;

        return $this;
    }

    public function getModoJuego(): ?string
    {
        return $this->modo_juego;
    }

    public function setModoJuego(string $modo_juego): self
    {
        $this->modo_juego = $modo_juego;

        return $this;
    }
}

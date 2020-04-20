<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoteRepository")
 */
class Note
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"details_eleve"})
     */
    private $note_value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Eleve", cascade={"all"},  inversedBy="notes")
     * @var Eleve
     */
    private $eleve;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Matiere", cascade={"all"}, inversedBy="notes")
     * @Groups({"details_eleve"})
     */
    private $matiere;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getNom() {
        return $this->getNoteValue();
    }

    public function getNoteValue(): ?int
    {
        return $this->note_value;
    }

    public function setNoteValue(int $note_value): self
    {
        $this->note_value = $note_value;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): self
    {
        $this->eleve = $eleve;

        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

}

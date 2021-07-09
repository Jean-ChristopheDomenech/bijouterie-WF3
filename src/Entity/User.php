<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="un compte existe deja avec cet email"
 * )
 * @method string getUserIdentifier()
 */
class User implements UserInterface
//notre "user implementents userinterface" herite obligatoirement des methodes de cette interface:
//getPassword(), getSalt() et getRoles(), eraseCredential(), getUsername

{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank("message=saisi le champs svp")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email("message=Merci de saisir un email valide")
     * @Assert\NotBlank("message=saisi le champs svp")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank("message=saisi le champs svp")
     * @Assert\EqualTo(propertyPath="confirmPassword, message="les mdp ne corresspondent pas")
     */
    private $password;

    public $confirmPassword;



    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="saisi le champs svp")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="saisi le champs svp")
     */
    private $prenom;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = ["ROLE_USER"];



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
        //permet de faire transiter le mdp en texte brut pour etre traité lors de l'encodage
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
        //vise a nettoyer les mdp en texte brut ds la BDD
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }
}

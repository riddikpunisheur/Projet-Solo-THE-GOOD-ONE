<?php

namespace App\Entity;

use App\Form\UserType;
use App\Entity\Activitys;
use App\Entity\Questions;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="ce compte existe déjà avec cet email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\NotBlank(allowNull=false, message="Prénom obligatoire")
     * @Assert\Length(
     *      min = 3,
     *      max = 64,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * )
     */

    private $first_name;

    /**
     * @ORM\Column(type="string", length=64 nullable=true)
     * @Assert\NotBlank(allowNull=false, message="Nom obligatoire")
     * @Assert\Length(
     *      min = 3,
     *      max = 64,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * )
     * 
     */

    private $last_name;

    /**
     * @ORM\Column(type="date", length=128)
     * @Assert\NotBlank(allowNull=false, message="La date de naissance est obligatoire")
     * @Assert\LessThan("-18 years", message="Vous devez être majeur pour vous inscrire")
     * 
     */

    private $birthdate;

    /**
     * @ORM\Column(type="json", length=128, unique=true)
     * @Assert\NotBlank(allowNull=false, message="Email obligatoire")
     * @Assert\Email(
     * message = "Email non valide.")
     */

    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(allowNull=false, message="veuillez entrer votre mot de passe")
     * @Assert\Regex(
     *  pattern = "#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)#",
     *  match=true,
     *  message="8 caractères minimum")
     * @Assert\Length(
     *  min = 8,
     *  minMessage="8 caractères minimum")
     */

    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    
    private $pictures;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(allowNull=false, message="Decrivez-vous pour nous permettre de mieux cibler vôtre recherche")
     * @Assert\Length(
     *      min = 150,
     *      max = 3000,
     *      minMessage = "{{ limit }} caractères minimum",
     *      maxMessage = "{{ limit }} caractères maximum",
     *      allowEmptyString = false
     * ) 
     */

    private $about;

    /**
     * @ORM\Column(type="datetime")
     */

    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="user", orphanRemoval=true)
     */
    private $questions;


    public function __construct()
    {
        //POUR PLUS TARD LES LANGUES
        //$this->language = new ArrayCollection();
        
        $this->first_name = new ArrayCollection();
        $this->last_name = new ArrayCollection();
        $this->email = new ArrayCollection();
        $this->birthdate = new \DateTime();
        $this->about = new ArrayCollection();
        //$this->username = '#'.random_int(1, 100000);
        $this->createdAt = new \DateTime;
        $this->isActive = true;
    }

    public function __toString()
    {
        return $this->about;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

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


    public function getPictures(): ?string
    {
        return $this->pictures;
    }

    public function setPictures(?string $pictures): self
    {
        $this->pictures = $pictures;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the value of isActive
     */ 
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set the value of isActive
     *
     * @return  self
     */ 
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get the value of questions
     */ 
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Set the value of questions
     *
     * @return  self
     */ 
    public function setQuestions($questions)
    {
        $this->questions = $questions;

        return $this;
    }


}

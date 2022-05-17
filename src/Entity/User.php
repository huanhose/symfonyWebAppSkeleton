<?php

namespace App\Entity;

use App\Entity\Exceptions\UserWithBlankFullNameException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Exceptions\WrongEmailUserException;
use App\Entity\Exceptions\UserWithBlankNameException;
use App\Repository\UserRepository;
use App\Service\Shared\DataValidator;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private DataValidator $dataValidator;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 50)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $fullName;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $dateCreated;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    public function __construct()
    {
        $this->dataValidator = new DataValidator();

        $this->dateCreated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        if (! $this->dataValidator->isEmail($email)) {
            throw WrongEmailUserException::onValue($email);
        }

        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $rol): self
    {
        if (! in_array($rol, $this->roles)) {
            $this->roles[] = $rol;
        }

        return $this;
    }

    public function deleteRole(string $rol): self
    {
        $index = array_search($rol, $this->roles);
        if (false !== $index) {
            array_splice($this->roles, $index, 1);
        }

        return $this;
    }

    /**
     * Get a list of defined system roles
     * System roles are internals and not used by the app
     *
     * @return array
     */
    public static function getDefinedSystemRoles(): array
    {
        return ['ROLE_USER', 'ROLE_VERIFIED_USER'];
    }

    /**
     * Get a list of App defined roles
     * Roles that have a sense in the domain of the App
     *
     * @return array
     */
    public static function getDefinedAppRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * Check is a role is an App role
     *
     * @param string $role
     * @return boolean
     */
    private static function isAppRole(string $role): bool
    {
        return in_array($role, static::getDefinedAppRoles());
    }

    /**
     * Return only user roles that are App roles
     *
     * @return array
     */
    public function getAppRoles(): array
    {
        return array_intersect(
            $this->getRoles(),
            static::getDefinedAppRoles()
        );
    }

    /**
     * Add an App role
     * If role not is an App role, we throw an Exception
     *
     * @param string $role
     * @return void
     */
    public function addAppRole(string $role)
    {
        if (! static::isAppRole($role)) {
            throw new \Exception(" $role can`t be added as an App role");
        }

        $this->addRole($role);
    }

    /**
     * Delete an App role
     * If role not is an App role, we throw an Exception
     *
     * @param string $role
     * @return void
     */
    public function deleteAppRole(string $role)
    {
        if (! static::isAppRole($role)) {
            throw new \Exception(" $role can`t be deleted as an App role");
        }

        $this->deleteRole($role);
    }

    /**
     * Set a list of App Roles
     * If an role in the list isn't an App role, an exception is thrown
     *
     * @param array $listAppRoles
     * @return void
     */
    public function setAppRoles(array $listAppRoles)
    {
        //Check roles to set are app roles
        foreach ($listAppRoles as $role) {
            if (! static::isAppRole($role)) {
                throw new \Exception("An system role $role can't be assigned to the user");
            }
        }

        $listUserSystemRoles = array_intersect(
            $this->getRoles(),
            static::getDefinedSystemRoles()
        );
        $newlistUserRoles = array_merge(
            $listAppRoles,
            $listUserSystemRoles
        );

        $this->setRoles($newlistUserRoles);
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        if ($this->dataValidator->isBlank($name, strict:true)) {
            throw UserWithBlankNameException::create();
        }

        $this->name = $name;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        if ($this->dataValidator->isBlank($fullName, strict:true)) {
            throw UserWithBlankFullNameException::create();
        }

        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        //When user is verified, he adquires ROLE_VERIFIED_USER rol
        if ($isVerified) {
            $this->addRole('ROLE_VERIFIED_USER');
        } else {
            $this->deleteRole('ROLE_VERIFIED_USER');
        }

        return $this;
    }
}

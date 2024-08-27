<?php

/**
 * @noinspection PhpUnusedAliasInspection
 */

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $user_id;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $token_expired_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * @var string|null
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $blocked;

    /**
     *
     */
    public const ROLE_USER = 'ROLE_USER';

    /**
     * User constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->setDateCreate();
        $this->roles = [self::ROLE_USER];
        $this->confirmed = false;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     *
     * @return $this
     * @throws Exception
     */
    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    /**
     * @param array $roles
     *
     * @return $this
     * @throws Exception
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @param string $username
     *
     * @return User
     * @throws Exception
     * @see UserInterface
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     * @throws Exception
     */
    public function setEmail(string $email = null): self
    {
        $this->email = $email;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     * @throws Exception
     */
    public function setPassword(string $password = null): self
    {
        $this->password = $password;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return $this
     * @throws Exception
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        if (!$this->date_update) {
            $this->setDateUpdate();
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getTokenExpiredAt(): ?DateTimeInterface
    {
        return $this->token_expired_at;
    }

    /**
     * @param int $token_expired_at
     *
     * @return $this
     */
    public function setTokenExpiredAt(int $token_expired_at): self
    {
        try {
            if (0 < (int)$token_expired_at) {
                $this->token_expired_at = new DateTime(date('Y-m-d H:i:s', $token_expired_at));
            }

            if (!$this->date_update) {
                $this->setDateUpdate();
            }
        } catch (Exception $exception) {
            trigger_error($exception);
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param $created_at
     *
     * @return $this
     */
    public function setCreatedAt($created_at): self
    {
        try {
            if ('' !== $created_at) {
                $dateObj = new DateTime($created_at);
                $this->created_at = new DateTime($dateObj->format('Y-m-d H:i:s'));

                if (!$this->date_update) {
                    $this->setDateUpdate();
                }
            }
        } catch (Exception $exception) {
            trigger_error($exception);
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param $updated_at
     *
     * @return $this
     */
    public function setUpdatedAt($updated_at): self
    {
        try {
            if ('' !== $updated_at) {
                $dateObj = new DateTime($updated_at);
                $this->updated_at = new DateTime($dateObj->format('Y-m-d H:i:s'));

                if (!$this->date_update) {
                    $this->setDateUpdate();
                }
            }
        } catch (Exception $exception) {
            trigger_error($exception);
        }

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
        $this->plainPassword = null;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateCreate(): ?DateTimeInterface
    {
        return $this->date_create;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function setDateCreate(): self
    {
        if (!$this->date_create) {
            $this->date_create = new DateTime('now', new \DateTimeZone('Europe/London'));

            if (!$this->date_update) {
                $this->setDateUpdate();
            }
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateUpdate(): ?DateTimeInterface
    {
        return $this->date_update;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function setDateUpdate(): self
    {
        $this->date_update = new DateTime('now', new \DateTimeZone('Europe/London'));

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     */
    public function setPlainPassword(string $password): void
    {
        $this->plainPassword = $password;
    }

    /**
     * @return bool|null
     */
    public function getBlocked(): ?bool
    {
        return $this->blocked;
    }

    /**
     * @param bool|null $blocked
     *
     * @return $this
     */
    public function setBlocked(?bool $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }
}

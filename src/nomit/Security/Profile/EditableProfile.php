<?php

namespace nomit\Security\Profile;

use Psr\Http\Message\UploadedFileInterface;

final class EditableProfile implements EditableProfileInterface
{

    public function __construct(
        private int $id,
        private string $username,
        private ?string $email,
        private ?string $password,
        private ?string $name,
        private ?string $description,
        private UploadedFileInterface|string|null $avatar,
        private UploadedFileInterface|string|null $banner
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setAvatar(string $avatar): EditableProfileInterface
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return UploadedFileInterface|string|null
     */
    public function getAvatar(): UploadedFileInterface|string|null
    {
        return $this->avatar;
    }

    public function setBanner(string $banner): EditableProfileInterface
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * @return UploadedFileInterface|string|null
     */
    public function getBanner(): UploadedFileInterface|string|null
    {
        return $this->banner;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'avatar' => $this->getAvatar(),
            'banner' => $this->getBanner()
        ];
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

}
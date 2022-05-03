<?php
namespace App\Service\User;

/**
 * DTO For RegisterUser service
 */
class CreateUserDTO
{
    public function __construct(
        public string $email,
        public string $name,
        public string $fullName,
        public string $password,
    )
    {}
}



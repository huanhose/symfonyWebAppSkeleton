<?php
namespace App\Service\User;

/**
 * DTO for ModifyUser service user case
 * 
 * We use null value in a property to notice we don't want to modify this field
 */
class ModifyUserDTO
{
    public function __construct(
        public int $id,
        public ?string $email = null,
        public ?string $name = null,
        public ?string $fullName = null,
    )
    {}
}
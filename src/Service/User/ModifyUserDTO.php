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
        public ?array $listAppRoles=null
    )
    {
        if (null !== $listAppRoles) {
            $this->listAppRoles = $this->sanitizeListRoles($listAppRoles);
        }
    }

    /**
     * Get only array values and convert items to string
     *
     * @param array $listRoles
     * @return array
     */
    private function sanitizeListRoles(array $listRoles):array
    {
        return array_map(
            function($item) {
                return (string) $item;
            },
            array_values($listRoles)
        );
    }
}
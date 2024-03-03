<?php


namespace App\DTO;

class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $fullname,
        public readonly bool $isOpenToWork,
        public readonly string $thumbnail,
        public readonly string $title,
        public readonly string $subtitle,
        public readonly int $experienceCount,
        public readonly int $projectCount
    ) {
    }
}

<?php

namespace App\DTO;


class SkillWithCountDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly int $countUsers
    ) {
    }
}

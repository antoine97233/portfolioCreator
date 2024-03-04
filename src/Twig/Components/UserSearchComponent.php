<?php



namespace App\Twig\Components;

use App\Repository\UserRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;


#[AsLiveComponent(name: 'user_search')]
final class UserSearchComponent
{
    use DefaultActionTrait;


    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @return array<User>
     */
    public function getUsers(): array
    {
        return $this->userRepository->findBySearchQuery($this->query);
    }
}

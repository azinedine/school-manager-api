<?php

namespace App\UseCases\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UpdateUserProfileUseCase
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(User $user, array $data): User
    {
        // Add any additional business logic or policy checks here if needed
        // For now, strict update via repo
        return $this->userRepository->update($user, $data);
    }
}

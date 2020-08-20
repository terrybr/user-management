<?php
declare(strict_types=1);

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserReaderRepository;

class UserReaderService
{
    private UserReaderRepository $repository;

    public function __construct(UserReaderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function get($id): array
    {
        return $this->repository->findById($id);
    }
}

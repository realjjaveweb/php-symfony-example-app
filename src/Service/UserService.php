<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\Boundaries\RegistrationInput;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;

class UserService
{
    public function __construct(
        // repository could also just be the repository interface
        // and then we would bind it to the target implementation
        // in config/services.yaml (seemed redundant for this showcase)
        private readonly UserRepository $userRepository,
    ) {
    }

    public function register(RegistrationInput $input): void
    {
        $user = new User(
            // for a readonly model we could just do
            // name: $input->name, ...
        );
        $user->setEmail($input->getEmail());
        $user->setRoles([$input->getRole()]);
        $user->setPassword($input->getPassword());
        $user->setName($input->getName());

        $this->userRepository->add($user, flush: true);
    }

    /**
     * Currently custom order is not implemented,
     * current order is by Name(ASC)
     * @return User[]
     */
    public function list(): array
    {
        // following gets ALL data with no pagination (basically ->findAll() + orderBy)
        // if we were about to introduce pagination
        // we could use ->findBy([], orderBy:, ..., limit: ..., offset: ...)
        return $this->userRepository->findBy([], orderBy: [
            $this->userRepository->getColumnName('name') => Criteria::ASC,
        ]);
    }
}

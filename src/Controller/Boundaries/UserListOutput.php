<?php

declare(strict_types=1);

namespace App\Controller\Boundaries;

use App\Entity\User;

class UserListOutput implements \JsonSerializable
{
    public readonly array $output;

    /**
     * @param User[]
     */
    public function __construct(array $users)
    {
        // readonly => have to use intermediate variable
        $preparedOutput = [];
        foreach ($users as $key => $user) {
            $preparedOutput[] = [
                'Name' => $user->getName(),
                'Email' => $user->getEmail(),
                'Roles' => $user->getRoles(),
            ];
            unset($users[$key]); // remove already loaded users from input array
        }
        $this->output = $preparedOutput;
    }

    public function jsonSerialize(): mixed
    {
        return $this->output;
    }
}

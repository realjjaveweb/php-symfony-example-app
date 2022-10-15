<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Boundaries\UserListOutput;
use App\Controller\Form\RegistrationForm;
use App\Service\UserService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends BaseController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        // service could also just be the service interface
        // and then we would bind it to the target implementation
        // in config/services.yaml (seemed redundant for this showcase)
        private readonly UserService $userService,
    ) {
    }

    public const URL_REGISTER = 'register';
    public const ROUTE_REGISTER = self::class . '_' . self::URL_REGISTER;
    #[Route('/' . self::URL_REGISTER, methods: ['POST'], name: self::ROUTE_REGISTER)]
    public function register(Request $request): JsonResponse
    {
        try {
            $form = $this->createForm(RegistrationForm::class);

            // could be a method
            $input = json_decode($request->getContent(), associative: true);
            if ($input === null) {
                return $this->getInvalidFormResponse(
                    errors: 'Unexpected input, try to reload page, if that doesn\'t help, please contant the administrator.'
                );
            }

            $form->submit($input, true);


            // in this case, isSubmitted is redundant because we submitted the data manually
            // but keeping it to clearly show what's happening
            if ($form->isSubmitted() && $form->isValid()) {
                $this->userService->register($form->getData());

                return new JsonResponse(
                    data: [
                        self::KEY_MESSAGE => 'Registration went well :-)',
                    ],
                    status: Response::HTTP_OK,
                );
            } else {
                return $this->getInvalidFormResponse(
                    errors: $this->getFormErrorsOutput($form),
                );
            }
        } catch (\Throwable $e) {
            $this->logger->error('Error during registration form processing', [$e]);
            return $this->getServerErrorResposne();
        }
    }

    public const URL_USER_LIST = 'list';
    public const ROUTE_USER_LIST = self::class . '_' . self::URL_USER_LIST;
    #[Route('/' . self::URL_USER_LIST, methods: ['GET'], name: self::ROUTE_USER_LIST)]
    public function list()
    {
        try {
            return new JsonResponse(
                data: [
                    self::KEY_DATA => new UserListOutput($this->userService->list()),
                ],
                status: Response::HTTP_OK,
            );
        } catch (\Throwable $e) {
            $this->logger->error('Error during getting the list of users', [$e]);
            return $this->getServerErrorResposne();
        }
    }
}

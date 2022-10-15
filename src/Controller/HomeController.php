<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Form\RegistrationForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends BaseController
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public const URL_HOME = '/';
    public const ROUTE_HOME = self::class . '_' . 'home';
    #[Route(self::URL_HOME, methods: ['GET'], name: self::ROUTE_HOME)]
    public function index(): Response
    {
        $form = $this->createForm(RegistrationForm::class);

        return $this->renderForm('home.html.twig', [
            'form' => $form,
            'user_list_url' => $this->urlGenerator->generate(
                name: UserController::ROUTE_USER_LIST,
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
            ),
        ]);
    }
}

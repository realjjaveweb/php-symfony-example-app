<?php

declare(strict_types=1);

namespace App\Controller\Form;

use App\Controller\Boundaries\RegistrationInput;
use App\Controller\UserController;
use App\Domain\Roles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationForm extends AbstractType
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => RegistrationInput::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => self::class,
        ]);
    }

    /** @inheritdoc */
    public function getBlockPrefix(): string
    {
        // we don't want the form data to be nested under a form name/key
        return '';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $commonOptions = [
            // possible common options
        ];
        $builder
            ->setMethod(Request::METHOD_POST)
            ->setAction($this->urlGenerator->generate(
                name: UserController::ROUTE_REGISTER,
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
            ))
            // keeping [] wraps even when not necessary, in case more options are needed
            ->add(RegistrationInput::FIELD_EMAIL, type: EmailType::class, options: [...$commonOptions])
            ->add(RegistrationInput::FIELD_ROLE, type: EnumType::class, options: ['class' => Roles::class, ...$commonOptions])
            ->add(RegistrationInput::FIELD_PASSWORD, type: PasswordType::class, options: [...$commonOptions])
            ->add(RegistrationInput::FIELD_NAME, type: TextType::class, options: [...$commonOptions])
            ->add('save', SubmitType::class, ['label' => 'Create User']);
    }
}

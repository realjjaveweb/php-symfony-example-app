<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    public const KEY_MESSAGE = 'message';
    public const KEY_ERROR_MESSAGES = 'error_messages';
    public const KEY_DATA = 'data';

    protected function getServerErrorResposne(
        ?string $message = null,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        return new JsonResponse(
            data: [
                self::KEY_MESSAGE => $message ?? 'There was an error on the server side. Please try again later.'
            ],
            status: $statusCode,
        );
    }

    protected function getInvalidFormResponse(string $errors = ""): JsonResponse
    {
        return new JsonResponse(
            data: [
                self::KEY_MESSAGE => 'The submited form was not valid.' . (empty($errors) ? '' : ('<br>' . $errors)),
            ],
            status: Response::HTTP_BAD_REQUEST,
        );
    }

    // sadly, Symfony forms don't have exactly a sane way how to get all the validation errors keyed by field names -_-
    /**
     * @return string[] keyed by the field names
     */
    private function getFlattenedErrorMessages(Form $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            // strtr does not search for already replaced values
            // but for our {{ placeholder }} which should be unique within the template anyways
            // is this just fine and a faster way
            $errors[$key] = strtr($error->getMessageTemplate(), $error->getMessageParameters());
        }

        if ($form->count() > 0) {
            foreach ($form as $childForm) {
                if (!$childForm->isValid()) {
                    $errors[$childForm->getName()] = implode(' ', $this->getFlattenedErrorMessages($childForm));
                }
            }
        }
        return $errors;
    }

    // $form->getErrors() result has also implemented __toString
    // so if we would be fine with it's string conversion we could just do (string)$form->getErrors() :-)
    // to get the nested fields we'd have to do (string)$form->getErrors(deep: true, flatten: false)
    /**
     * Supports deep error list - either flattened or till 2nd nested level
     */
    protected function getFormErrorsOutput(Form $form): string
    {
        $errorMsgs = $this->getFlattenedErrorMessages($form);

        //print_r($errorMsgs);exit;

        return $this->render('messages/formErrors.html.twig', parameters: [
            self::KEY_ERROR_MESSAGES => $errorMsgs,
        ])->getContent() ?: ''; // Let's Elvis! ?:-)
    }
}

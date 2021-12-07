<?php

namespace App\Controller\Task;

use App\Request\Task\ListRequest;
use App\Request\Task\PostRequest;
use App\Service\Task\TaskService;
use App\Utils\AppException;
use App\Utils\RequestValidator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    public const UNEXPECTED_EXCEPTION = "Unexpected error, please contact the admin";

    private RequestValidator $validator;

    private TaskService $service;

    public function __construct(RequestValidator $validator, TaskService $service)
    {
        $this->validator = $validator;
        $this->service   = $service;
    }
    /**
     * @Route("/tasks", name="task.list", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function executeList(Request $request): Response
    {
        try {
            return $this->json(
                $this->service->list($this->validator->process(new ListRequest($request)))
            );
        } catch (AppException $appException) {
            return $this->json(['error' => $appException->getMessage()], $appException->getCode());
        } catch (Exception $exception) {
            return $this->json(['error' => self::UNEXPECTED_EXCEPTION], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/tasks", name="task.post", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function executePost(Request $request): Response
    {
        try {
            return $this->json(
                $this->service->post($this->validator->process(new PostRequest($request)))
            );
        } catch (AppException $appException) {
            return $this->json(['error' => $appException->getMessage()], $appException->getCode());
        } catch (Exception $exception) {
            return $this->json(['error' => self::UNEXPECTED_EXCEPTION], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace App\Request\Task;

use App\Request\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class PostRequest extends RequestModel
{
    /**
     * @Assert\NotBlank(message = "summary parameter must be present")
     * @Assert\Length(max = 2500, maxMessage = "summary cannot be longer than {{ limit }} characters")
     */
    protected ?string $summary;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $body = json_decode($request->getContent(), true);
        $this->summary = $body['summary'] ?? null;
    }

    public function getSummary(): ?string
    {
        return $this->summary ?? null;
    }
}

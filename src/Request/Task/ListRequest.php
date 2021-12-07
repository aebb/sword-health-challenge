<?php

namespace App\Request\Task;

use App\Request\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ListRequest extends RequestModel
{
    /**
     * @Assert\Positive(message = "start parameter must be a positive integer")
     */
    protected ?string $start;

    /**
     * @Assert\Positive(message = "count parameter must be a positive integer")
     */
    protected ?string $count;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->start = $request->query->get('start');
        $this->count  = $request->query->get('count');
    }

    public function getStart(): ?int
    {
        return $this->start ?? null;
    }

    public function getCount(): ?int
    {
        return $this->count ?? null;
    }
}

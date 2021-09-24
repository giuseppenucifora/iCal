<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) 2021 Markus Poerschke <markus@poerschke.nrw>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal\Domain\Entity;

use Eluceo\iCal\Domain\Collection\Events;
use Eluceo\iCal\Domain\Collection\EventsArray;
use Eluceo\iCal\Domain\Collection\EventsGenerator;
use Eluceo\iCal\Domain\Enum\Method;
use Eluceo\iCal\Domain\Enum\Status;
use InvalidArgumentException;
use Iterator;

class Calendar
{
    private string $productIdentifier = '-//eluceo/ical//2.0/EN';

    private string $calId = '';

    private string $name = '';

    private string $description = '';

    private Events $events;
    private ?Status $status = null;
    private ?Method $method = null;

    /**
     * @var array<TimeZone>
     */
    private array $timeZones = [];

    /**
     * @param Event[]|Iterator<Event>|Events $events
     */
    public function __construct($events = [])
    {
        $this->events = $this->ensureEventsObject($events);
    }

    /**
     * @param Event[]|Iterator<Event>|Events $events
     */
    private function ensureEventsObject($events = []): Events
    {
        if ($events instanceof Events) {
            return $events;
        }

        if (is_array($events)) {
            return new EventsArray($events);
        }

        if ($events instanceof Iterator) {
            return new EventsGenerator($events);
        }

        throw new InvalidArgumentException('$events must be an array, an object implementing Iterator or an instance of Events.');
    }

    public function getProductIdentifier(): string
    {
        return $this->productIdentifier;
    }

    public function setProductIdentifier(string $productIdentifier): self
    {
        $this->productIdentifier = $productIdentifier;

        return $this;
    }

    public function getCalId(): string
    {
        return $this->calId;
    }

    public function setCalId(string $calId): self
    {
        $this->calId = $calId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEvents(): Events
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        $this->events->addEvent($event);

        return $this;
    }

    /**
     * @return array<TimeZone>
     */
    public function getTimeZones(): array
    {
        return $this->timeZones;
    }

    public function addTimeZone(TimeZone $timeZone): self
    {
        $this->timeZones[] = $timeZone;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function hasStatus(): bool
    {
        return $this->status !== null;
    }

    public function setStatus(?Status $status): Calendar
    {
        $this->status = $status;

        return $this;
    }

    public function getMethod(): ?Method
    {
        return $this->method;
    }

    public function hasMethod(): bool
    {
        return $this->method !== null;
    }

    public function setMethod(?Method $method): Calendar
    {
        $this->method = $method;

        return $this;
    }
}

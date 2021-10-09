<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) 2021 Markus Poerschke <markus@poerschke.nrw>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal\Presentation\Factory;

use DateInterval;
use Eluceo\iCal\Domain\Collection\Events;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\MultiDay;
use Eluceo\iCal\Domain\ValueObject\Occurrence;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Component;
use Eluceo\iCal\Presentation\Component\Property;
use Eluceo\iCal\Presentation\Component\Property\Value\BooleanValue;
use Eluceo\iCal\Presentation\Component\Property\Value\DateTimeValue;
use Eluceo\iCal\Presentation\Component\Property\Value\DateValue;
use Eluceo\iCal\Presentation\Component\Property\Value\GeoValue;
use Eluceo\iCal\Presentation\Component\Property\Value\TextValue;
use Generator;

/**
 * @SuppressWarnings("CouplingBetweenObjects")
 */
class EventFactory
{
    /**
     * @return Generator<Component>
     */
    final public function createComponents(Events $events): Generator
    {
        foreach ($events as $event) {
            yield $this->createComponent($event);
        }
    }

    public function createComponent(Event $event): Component
    {
        return new Component('VEVENT', iterator_to_array($this->getProperties($event), false));
    }

    /**
     * @return Generator<Property>
     */
    private function getProperties(Event $event): Generator
    {
        yield new Property('UID', new TextValue((string) $event->getUniqueIdentifier()));
        yield new Property('DTSTAMP', new DateTimeValue($event->getTouchedAt()));

        if ($event->hasSummary()) {
            yield new Property('SUMMARY', new TextValue($event->getSummary()));
        }

        if ($event->hasDescription()) {
            yield new Property('DESCRIPTION', new TextValue($event->getDescription()));
        }

        if ($event->hasOccurrence()) {
            yield from $this->getOccurrenceProperties($event->getOccurrence());
        }

        if ($event->hasLocation()) {
            yield from $this->getLocationProperties($event);
        }
    }

    /**
     * @return Generator<Property>
     */
    private function getOccurrenceProperties(Occurrence $occurrence): Generator
    {
        if ($occurrence instanceof SingleDay) {
            yield new Property('DTSTART', new DateValue($occurrence->getDate()));
            // see https://docs.microsoft.com/en-us/openspecs/exchange_server_protocols/ms-oxcical/0f262da6-c5fd-459e-9f18-145eba86b5d2
            yield new Property('X-MICROSOFT-CDO-ALLDAYEVENT', new BooleanValue(true));
        }

        if ($occurrence instanceof MultiDay) {
            yield new Property('DTSTART', new DateValue($occurrence->getFirstDay()));
            yield new Property('DTEND', new DateValue($occurrence->getLastDay()->add(new DateInterval('P1D'))));
            // see https://docs.microsoft.com/en-us/openspecs/exchange_server_protocols/ms-oxcical/0f262da6-c5fd-459e-9f18-145eba86b5d2
            yield new Property('X-MICROSOFT-CDO-ALLDAYEVENT', new BooleanValue(true));
        }

        if ($occurrence instanceof TimeSpan) {
            yield new Property('DTSTART', new DateTimeValue($occurrence->getBegin()));
            yield new Property('DTEND', new DateTimeValue($occurrence->getEnd()));
        }
    }

    /**
     * @return Generator<Property>
     */
    private function getLocationProperties(Event $event): Generator
    {
        yield new Property('LOCATION', new TextValue((string) $event->getLocation()));

        if ($event->getLocation()->hasGeographicalPosition()) {
            yield new Property('GEO', new GeoValue($event->getLocation()->getGeographicPosition()));
        }
    }
}

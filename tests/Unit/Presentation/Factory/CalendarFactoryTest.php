<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) 2021 Markus Poerschke <markus@poerschke.nrw>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eluceo\iCal\Test\Unit\Presentation\Factory;

use DateTimeImmutable;
use DateTimeZone;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Timestamp;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Presentation\ContentLine;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use PHPUnit\Framework\TestCase;

class CalendarFactoryTest extends TestCase
{
    public function testRenderEmptyCalendar()
    {
        $calendar = new Calendar();
        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VCALENDAR',
            'PRODID:' . $calendar->getProductIdentifier(),
            'VERSION:2.0',
            'CALSCALE:GREGORIAN',
            'END:VCALENDAR',
            '',
        ]);

        self::assertSame($expected, (string) (new CalendarFactory())->createCalendar($calendar));
    }

    public function testRenderCalendarWithName()
    {
        $calendar = new Calendar();
        $calendar->setName('TEST_NAME');
        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VCALENDAR',
            'PRODID:' . $calendar->getProductIdentifier(),
            'VERSION:2.0',
            'CALSCALE:GREGORIAN',
            'X-WR-CALNAME:' . $calendar->getName(),
            'END:VCALENDAR',
            '',
        ]);

        self::assertSame($expected, (string) (new CalendarFactory())->createCalendar($calendar));
    }

    public function testRenderCalendarWithDescription()
    {
        $calendar = new Calendar();
        $calendar->setDescription('TEST_DESCRIPTION');
        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VCALENDAR',
            'PRODID:' . $calendar->getProductIdentifier(),
            'VERSION:2.0',
            'CALSCALE:GREGORIAN',
            'X-WR-CALDESC:' . $calendar->getDescription(),
            'END:VCALENDAR',
            '',
        ]);

        self::assertSame($expected, (string) (new CalendarFactory())->createCalendar($calendar));
    }

    public function testRenderCalendarWithId()
    {
        $calendar = new Calendar();
        $calendar->setCalId('TEST_ID');
        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VCALENDAR',
            'PRODID:' . $calendar->getProductIdentifier(),
            'VERSION:2.0',
            'CALSCALE:GREGORIAN',
            'X-WR-RELCALID:' . $calendar->getCalId(),
            'END:VCALENDAR',
            '',
        ]);

        self::assertSame($expected, (string) (new CalendarFactory())->createCalendar($calendar));
    }

    public function testRenderWithEvents()
    {
        $currentTime = new Timestamp(
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                '2019-11-10 11:22:33',
                new DateTimeZone('UTC')
            )
        );
        $calendar = new Calendar(
            [
                (new Event(new UniqueIdentifier('event1')))->touch($currentTime),
                (new Event(new UniqueIdentifier('event2')))->touch($currentTime),
            ]
        );
        $calendar->setProductIdentifier('-//test/ical//2.0/EN');

        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VCALENDAR',
            'PRODID:-//test/ical//2.0/EN',
            'VERSION:2.0',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'UID:event1',
            'DTSTAMP:20191110T112233Z',
            'END:VEVENT',
            'BEGIN:VEVENT',
            'UID:event2',
            'DTSTAMP:20191110T112233Z',
            'END:VEVENT',
            'END:VCALENDAR',
            '',
        ]);

        self::assertSame($expected, (string) (new CalendarFactory())->createCalendar($calendar));
    }
}

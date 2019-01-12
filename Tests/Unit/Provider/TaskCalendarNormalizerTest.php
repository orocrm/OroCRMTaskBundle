<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\Provider;

use Doctrine\ORM\AbstractQuery;
use Oro\Bundle\ReminderBundle\Entity\Manager\ReminderManager;
use Oro\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\TaskBundle\Provider\TaskCalendarNormalizer;

class TaskCalendarNormalizerTest extends \PHPUnit\Framework\TestCase
{
    /** @var ReminderManager|\PHPUnit\Framework\MockObject\MockObject */
    protected $reminderManager;

    /** @var TaskCalendarNormalizer */
    protected $normalizer;

    protected function setUp()
    {
        $this->reminderManager = $this->getMockBuilder(ReminderManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->normalizer = new TaskCalendarNormalizer($this->reminderManager);
    }

    /**
     * @dataProvider getTasksProvider
     * @param array $tasks
     * @param array $expected
     */
    public function testGetTasks(array $tasks, array $expected)
    {
        $calendarId = 123;

        $query = $this->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->setMethods(['getArrayResult'])
            ->getMockForAbstractClass();

        $query->expects($this->once())
            ->method('getArrayResult')
            ->will($this->returnValue($tasks));

        $this->reminderManager->expects($this->once())
            ->method('applyReminders')
            ->with($expected, Task::class);

        $result = $this->normalizer->getTasks($calendarId, $query);
        self::assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getTasksProvider()
    {
        $createdDate = new \DateTime();
        $updatedDate = $createdDate->add(new \DateInterval('PT10S'));
        $startDate   = $createdDate->add(new \DateInterval('PT1H'));
        $end         = clone($startDate);
        $endDate     = $end->add(new \DateInterval('PT30M'));

        return [
            [
                'tasks'    => [
                    [
                        'id'          => 1,
                        'subject'     => 'test_subject',
                        'description' => 'test_description',
                        'dueDate'     => $startDate,
                        'createdAt'   => $createdDate,
                        'updatedAt'   => $updatedDate,
                    ]
                ],
                'expected' => [
                    [
                        'calendar'    => 123,
                        'id'          => 1,
                        'title'       => 'test_subject',
                        'description' => 'test_description',
                        'start'       => $startDate->format('c'),
                        'end'         => $endDate->format('c'),
                        'allDay'      => false,
                        'createdAt'   => $createdDate->format('c'),
                        'updatedAt'   => $updatedDate->format('c'),
                        'editable'    => false,
                        'removable'   => false,
                    ]
                ],
            ],
        ];
    }
}

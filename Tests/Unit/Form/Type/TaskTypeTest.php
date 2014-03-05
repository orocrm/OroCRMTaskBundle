<?php

namespace OroCRM\Bundle\TaskBundle\Tests\Unit\Form\Type;

use OroCRM\Bundle\TaskBundle\Form\Type\TaskType;

class TaskTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskType
     */
    protected $formType;

    protected function setUp()
    {
        $this->formType = new TaskType();
    }

    /**
     * @param $widgets
     *
     * @dataProvider widgetProvider
     */
    public function testBuildForm($widgets)
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->exactly(8))
            ->method('add')
            ->will($this->returnSelf());

        foreach ($widgets as $key => $widget) {
            $builder->expects($this->at($key))
                ->method('add')
                ->with($this->equalTo($widget))
                ->will($this->returnSelf());
        }

        $this->formType->buildForm($builder, []);
    }

    public function widgetProvider()
    {
        return [
            'all' => [
                'widgets' => [
                    'subject',
                    'description',
                    'dueDate',
                    'taskPriority',
                    'assignedTo',
                    'relatedAccount',
                    'relatedContact',
                    'owner',
                ]
            ]
        ];
    }

    public function testGetName()
    {
        $this->assertEquals('orocrm_task', $this->formType->getName());
    }
}

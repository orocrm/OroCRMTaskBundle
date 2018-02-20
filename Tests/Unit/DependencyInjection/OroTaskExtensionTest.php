<?php

namespace Oro\Bundle\TaskBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\TaskBundle\DependencyInjection\OroTaskExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OroTaskExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OroTaskExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new OroTaskExtension();
    }

    public function testLoad()
    {
        $this->extension->load([], $this->container);
        $this->assertTrue($this->container->getParameter('oro_task.calendar_provider.my_tasks.enabled'));
    }

    public function testLoadWithConfigs()
    {
        $this->extension->load(
            [
                ['my_tasks_in_calendar' => false]
            ],
            $this->container
        );
        $this->assertFalse($this->container->getParameter('oro_task.calendar_provider.my_tasks.enabled'));
    }
}

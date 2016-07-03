<?php

namespace OroCRM\Bundle\TaskBundle\Migrations\Schema\v1_11;

use Doctrine\DBAL\ConnectionException;

use Psr\Log\LoggerInterface;

use Oro\Bundle\MigrationBundle\Migration\ParametrizedMigrationQuery;

class UpdateReminderEmailTemplates extends ParametrizedMigrationQuery
{
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Update date format in reminder email templates using recipient organization localization settings';
    }

    /**
     * {@inheritdoc}
     */
    public function execute(LoggerInterface $logger)
    {
        $pattern = "|date('F j, Y, g:i A')";
        $patternCalendarRange[] = 'calendar_date_range(entity.start, entity.end, entity.allDay, 1)';
        $patternCalendarRange[] = "calendar_date_range(entity.start, entity.end, entity.allDay, 'F j, Y', 1)";
        $replacementTask = "|oro_format_datetime_organization({'organization': entity.organization})";
        $replacementCalendarRangeTask = 'calendar_date_range_organization(entity.start,' .
            ' entity.end, entity.allDay, 1, null, null, null, entity.organization)';
        $this->updateReminderTemplates(
            $logger,
            'task_reminder',
            $pattern,
            $replacementTask,
            $patternCalendarRange,
            $replacementCalendarRangeTask
        );
    }

    /**
     * @param LoggerInterface $logger
     * @param string $templateName
     * @param string|array $pattern
     * @param string $replacement
     * @param string|array $patternCalendarRange
     * @param string $replacementCalendarRange
     * @throws \Exception
     * @throws ConnectionException
     */
    protected function updateReminderTemplates(
        LoggerInterface $logger,
        $templateName,
        $pattern,
        $replacement,
        $patternCalendarRange,
        $replacementCalendarRange
    ) {
        $sql = 'SELECT * FROM oro_email_template WHERE name = :name ORDER BY id';
        $parameters = ['name' => $templateName];
        $types = ['name' => 'string'];

        $this->logQuery($logger, $sql, $parameters, $types);
        $templates = $this->connection->fetchAll($sql, $parameters, $types);

        try {
            $this->connection->beginTransaction();
            foreach ($templates as $template) {
                $subject = str_replace($pattern, $replacement, $template['subject']);
                $content = str_replace($pattern, $replacement, $template['content']);
                $content = str_replace($patternCalendarRange, $replacementCalendarRange, $content);
                $this->connection->update(
                    'oro_email_template',
                    ['subject' => $subject, 'content' => $content],
                    ['id' => $template['id']]
                );
            }
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}

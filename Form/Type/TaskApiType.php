<?php

namespace Oro\Bundle\TaskBundle\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroDateTimeType;
use Oro\Bundle\SoapBundle\Form\EventListener\PatchSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskApiType extends TaskType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'createdAt',
            OroDateTimeType::class,
            [
                'required' => false,
            ]
        );

        $builder->addEventSubscriber(new PatchSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Oro\Bundle\TaskBundle\Entity\Task',
                'csrf_token_id' => 'task',
                'csrf_protection' => false
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'task';
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addDueDateField(FormBuilderInterface $builder)
    {
        // no any additional constraints for "dueDate" in API
        $builder
            ->add(
                'dueDate',
                OroDateTimeType::class,
                ['required' => false]
            );
    }

    /**
     * @param FormEvent $event
     */
    protected function updateDueDateFieldConstraints(FormEvent $event)
    {
        // no any additional constraints for "dueDate" in API
    }
}

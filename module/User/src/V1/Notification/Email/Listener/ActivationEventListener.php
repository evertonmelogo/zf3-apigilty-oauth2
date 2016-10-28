<?php
namespace User\V1\Notification\Email\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;
use User\V1\UserActivationEvent;

class ActivationEventListener extends AbstractListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            UserActivationEvent::EVENT_ACTIVATE_USER_SUCCESS,
            [$this, 'sendActivationEmail'],
            499
        );
    }

    /**
     * Rund Console to Send Activation Email
     *
     * @param EventInterface $event
     */
    public function sendActivationEmail($event)
    {
        $emailAddress = $event->getParams()->getUserActivationEntity()->getUser()->getUsername();
        // command: v1 user send-activation-email <emailAddress>
        $cli = $this->phpProcessBuilder
                ->setArguments(['v1', 'user', 'send-activation-email', $emailAddress])
                ->getProcess();
        $cli->start();
        $pid = $cli->getPid();
    }
}

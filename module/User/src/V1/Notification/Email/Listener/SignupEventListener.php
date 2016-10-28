<?php
namespace User\V1\Notification\Email\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;
use User\V1\SignupEvent;

class SignupEventListener extends AbstractListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            SignupEvent::EVENT_INSERT_USER_SUCCESS,
            [$this, 'sendWelcomeEmail'],
            499
        );
    }

    /**
     * Rund Console to Send Activation Email
     *
     * @param EventInterface $event
     */
    public function sendWelcomeEmail($event)
    {
        $emailAddress = $event->getParams()->getUserEntity()->getUsername();
        $userActivationKey = $event->getParams()->getUserActivationKey();
        // command: v1 user send-welcome-email <emailAddress> <activationCode>
        $cli = $this->phpProcessBuilder
                ->setArguments(['v1', 'user', 'send-welcome-email', $emailAddress, $userActivationKey])
                ->getProcess();
        $cli->start();
        $pid = $cli->getPid();
    }
}

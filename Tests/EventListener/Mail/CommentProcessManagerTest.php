<?php
/*
 * Copyright (c) 2014 Eltrino LLC (http://eltrino.com)
 *
 * Licensed under the Open Software License (OSL 3.0).
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://opensource.org/licenses/osl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 */
namespace Diamante\DeskBundle\Tests\EventListener\Mail;

use Diamante\DeskBundle\EventListener\Mail\CommentProcessManager;
use Diamante\DeskBundle\Model\Ticket\Status;
use Eltrino\PHPUnit\MockAnnotations\MockAnnotations;
use Diamante\DeskBundle\Model\User\User as DiamanteUser;

class CommentProcessManagerTest extends \PHPUnit_Framework_TestCase
{
    const DUMMY_USER_ID = 1;

    /**
     * @var CommentProcessManager
     */
    private $commentProcessManager;

    /**
     * @var \Twig_Environment
     * @Mock \Twig_Environment
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     * @Mock \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $senderEmail;

    /**
     * @var \Swift_Message
     * @Mock \Swift_Message
     */
    private $message;

    /**
     * @var \Oro\Bundle\SecurityBundle\SecurityFacade
     * @Mock \Oro\Bundle\SecurityBundle\SecurityFacade
     */
    private $securityFacade;

    /**
     * @var \Diamante\DeskBundle\Model\Ticket\Notifications\Events\CommentWasAddedToTicket
     * @Mock \Diamante\DeskBundle\Model\Ticket\Notifications\Events\CommentWasAddedToTicket
     */
    private $commentWasAddedToTicketEvent;

    /**
     * @var \Diamante\DeskBundle\Model\Ticket\Notifications\Events\CommentWasUpdated
     * @Mock \Diamante\DeskBundle\Model\Ticket\Notifications\Events\CommentWasUpdated
     */
    private $commentWasUpdatedEvent;

    /**
     * @var \Diamante\DeskBundle\Model\Ticket\Notifications\Events\TicketStatusWasChanged
     * @Mock \Diamante\DeskBundle\Model\Ticket\Notifications\Events\TicketStatusWasChanged
     */
    private $ticketStatusWasChangedEvent;

    /**
     * @var \Diamante\DeskBundle\Model\Ticket\Notifications\Events\AttachmentWasAddedToComment
     * @Mock \Diamante\DeskBundle\Model\Ticket\Notifications\Events\AttachmentWasAddedToComment
     */
    private $attachmentWasAddedToCommentEvent;

    /**
     * @var array
     */
    private $recipientsList;

    /**
     * @var \Oro\Bundle\UserBundle\Entity\User
     * @Mock \Oro\Bundle\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \Oro\Bundle\ConfigBundle\Config\ConfigManager
     * @Mock \Oro\Bundle\ConfigBundle\Config\ConfigManager
     */
    private $configManager;

    /**
     * @var \Diamante\DeskBundle\Model\User\UserDetailsService
     * @Mock Diamante\DeskBundle\Model\User\UserDetailsService
     */
    private $userDetailsService;

    /**
     * @var \Diamante\DeskBundle\Model\User\UserDetails
     * @Mock Diamante\DeskBundle\Model\User\UserDetails
     */
    private $userDetails;

    protected function setUp()
    {
        MockAnnotations::init($this);

        $this->senderEmail = 'no-reply@example.com';
        $this->recipientsList = array(
            new DiamanteUser(1, DiamanteUser::TYPE_DIAMANTE),
            new DiamanteUser(1, DiamanteUser::TYPE_ORO),
        );

        $this->commentProcessManager = new CommentProcessManager(
            $this->twig,
            $this->mailer,
            $this->securityFacade,
            $this->configManager,
            $this->userDetailsService,
            $this->senderEmail
        );
    }

    public function testIsAnEventSubscriber()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->commentProcessManager);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(
                'commentWasAddedToTicket'     => 'onCommentWasAddedToTicket',
                'commentWasUpdated'           => 'onCommentWasUpdated',
                'ticketStatusWasChanged'      => 'onTicketStatusWasChanged',
                'attachmentWasAddedToComment' => 'onAttachmentWasAddedToComment',
            ),
            $this->commentProcessManager->getSubscribedEvents()
        );
    }

    public function testOnCommentWasAddedToTicket()
    {
        $this->commentWasAddedToTicketEvent
            ->expects($this->atLeastOnce())
            ->method('getEventName');

        $this->commentWasAddedToTicketEvent
            ->expects($this->any())
            ->method('getRecipientsList')
            ->will($this->returnValue($this->recipientsList));

        $this->commentWasAddedToTicketEvent
            ->expects($this->once())
            ->method('getCommentContent')
            ->will($this->returnValue('Comment Content'));

        $this->userDetailsService
            ->expects($this->any(0))
            ->method('fetch')
            ->will($this->returnValue($this->userDetails));

        $this->userDetails
            ->expects($this->at(0))
            ->method('getEmail')
            ->will($this->returnValue('no-reply.reporter@example.com'));

        $this->userDetails
            ->expects($this->at(1))
            ->method('getEmail')
            ->will($this->returnValue('no-reply.assignee@example.com'));

        $this->commentProcessManager->onCommentWasAddedToTicket($this->commentWasAddedToTicketEvent);
    }

    public function testOnCommentWasUpdated()
    {
        $this->commentWasUpdatedEvent
            ->expects($this->atLeastOnce())
            ->method('getEventName');

        $this->commentWasUpdatedEvent
            ->expects($this->any())
            ->method('getRecipientsList')
            ->will($this->returnValue($this->recipientsList));

        $this->commentWasUpdatedEvent
            ->expects($this->once())
            ->method('getCommentContent')
            ->will($this->returnValue('Comment Content'));

        $this->userDetailsService
            ->expects($this->any(0))
            ->method('fetch')
            ->will($this->returnValue($this->userDetails));

        $this->userDetails
            ->expects($this->at(0))
            ->method('getEmail')
            ->will($this->returnValue('no-reply.reporter@example.com'));

        $this->userDetails
            ->expects($this->at(1))
            ->method('getEmail')
            ->will($this->returnValue('no-reply.assignee@example.com'));

        $this->commentProcessManager->onCommentWasUpdated($this->commentWasUpdatedEvent);
    }

    public function testOnTicketStatusWasChanged()
    {
        $this->ticketStatusWasChangedEvent
            ->expects($this->atLeastOnce())
            ->method('getEventName');

        $this->ticketStatusWasChangedEvent
            ->expects($this->any())
            ->method('getRecipientsList')
            ->will($this->returnValue($this->recipientsList));

        $this->ticketStatusWasChangedEvent
            ->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue(new Status(Status::OPEN)));

        $this->userDetailsService
            ->expects($this->any(0))
            ->method('fetch')
            ->will($this->returnValue($this->userDetails));

        $this->userDetails
            ->expects($this->at(0))
            ->method('getEmail')
            ->will($this->returnValue('no-reply.reporter@example.com'));

        $this->userDetails
            ->expects($this->at(1))
            ->method('getEmail')
            ->will($this->returnValue('no-reply.assignee@example.com'));

        $this->commentProcessManager->onTicketStatusWasChanged($this->ticketStatusWasChangedEvent);
    }

    public function testOnAttachmentWasAddedToComment()
    {
        $this->attachmentWasAddedToCommentEvent
            ->expects($this->atLeastOnce())
            ->method('getEventName');

        $this->attachmentWasAddedToCommentEvent
            ->expects($this->any())
            ->method('getRecipientsList')
            ->will($this->returnValue($this->recipientsList));

        $this->attachmentWasAddedToCommentEvent
            ->expects($this->once())
            ->method('getAttachmentName')
            ->will($this->returnValue('attachmentName'));

        $this->userDetailsService
            ->expects($this->any(0))
            ->method('fetch')
            ->will($this->returnValue($this->userDetails));

        $this->userDetails
            ->expects($this->at(0))
            ->method('getEmail')
            ->will($this->returnValue('no-reply.reporter@example.com'));

        $this->userDetails
            ->expects($this->at(1))
            ->method('getEmail')
            ->will($this->returnValue('no-reply.assignee@example.com'));

        $this->commentProcessManager->onAttachmentWasAddedToComment($this->attachmentWasAddedToCommentEvent);
    }

    public function testProcess()
    {
        $this->configManager
            ->expects($this->once())
            ->method('get')
            ->with('diamante_desk.email_notification')
            ->will($this->returnValue(true));

        $this->securityFacade
            ->expects($this->exactly(2))
            ->method('getLoggedUser')
            ->will($this->returnValue($this->user));

        $this->user
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::DUMMY_USER_ID));

        $userVO = new DiamanteUser($this->user->getId(), DiamanteUser::TYPE_ORO);

        $this->userDetailsService
            ->expects($this->any())
            ->method('fetch')
            ->with($this->equalTo($userVO))
            ->will($this->returnValue($this->userDetails));

        $this->userDetails
            ->expects($this->any())
            ->method('getFullName')
            ->will($this->returnValue('FistName LastName'));

        $userFullName = $this->userDetails->getFullName();

        $options = array(
            'changes'     => array(),
            'attachments' => array(),
            'user'        => $userFullName,
            'header'      => null
        );

        $templates = array(
            'txt'  => 'DiamanteDeskBundle:Comment/notification/mails/update:notification.txt.twig',
            'html' => 'DiamanteDeskBundle:Comment/notification/mails/update:notification.html.twig'
        );

        $this->twig
            ->expects($this->exactly(2))
            ->method('render')
            ->will(
                $this->returnValueMap(
                    array(
                        array($templates['txt'], $options, 'test'),
                        array($templates['html'], $options, '<p>test</p>')
                    )
                )
            );

        $this->mailer
            ->expects($this->once())
            ->method('createMessage')
            ->will($this->returnValue($this->message));

        $this->message->expects($this->once())
            ->method('setSubject');

        $this->message->expects($this->once())
            ->method('setFrom')
            ->with($this->senderEmail, $userFullName);

        $this->message->expects($this->once())
            ->method('setTo')
            ->with($this->recipientsList);

        $this->message->expects($this->once())
            ->method('setBody')
            ->with('test', 'text/plain');

        $this->message->expects($this->once())
            ->method('addPart')
            ->with('<p>test</p>', 'text/html');

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->message);

        $this->commentProcessManager->setRecipientsList($this->recipientsList);
        $this->commentProcessManager->process();
    }
} 
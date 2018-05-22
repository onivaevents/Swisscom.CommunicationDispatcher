<?php

namespace Swisscom\CommunicationDispatcher\Tests\Functional;

/*
 * This file is part of the Swisscom.CommunicationDispatcher package.
 */

use Swisscom\CommunicationDispatcher\Dispatcher\Dispatcher;
use TYPO3\Flow\Tests\FunctionalTestCase;
use TYPO3\Fluid\View\StandaloneView;

/**
 * Class DispatcherTest
 * @package Swisscom\CommunicationDispatcher\Tests\Functional
 */
class DispatcherTest extends FunctionalTestCase
{

    /**
     * @var Dispatcher|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->dispatcher = $this->getAccessibleMock(Dispatcher::class, array('dummy'));
        $view = $this->objectManager->get(StandaloneView::class);
        $this->inject($this->dispatcher, 'settings', array('templateSourceNamespaces' => array(), 'partialRootPath' => null));
        $this->inject($this->dispatcher, 'view', $view);
    }

    /**
     * @test
     */
    public function dispatcherRendersText()
    {
        $params['event']['title'] = 'Foo';
        $renderedText = $this->dispatcher->_call('render', '{event.title} bar', $params);

        $this->assertSame('Foo bar', $renderedText);
    }
}
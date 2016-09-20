<?php
use SktT1Byungi\Session\Handler\File as FileHandler;
use SktT1Byungi\Session\Session;

class SessionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Session::manager()->destroy();
    }

    public function sessionStart()
    {
        Session::manager()->handler(new FileHandler("./.session/"))->start();
    }

    public function testStart()
    {
        $this->sessionStart();
        $this->assertEquals(session_status(), PHP_SESSION_ACTIVE);
        $this->assertTrue(Session::manager()->isStarted());
    }

    public function testFileHandler()
    {
        $this->sessionStart();

        Session::set('test', 1);
        Session::manager()->close();

        $path = './.session/sess_' . Session::manager()->id();

        $this->assertTrue(is_file($path));

        Session::manager()->destroy();

        $this->assertFalse(is_file($path));
    }

    public function testSessionId()
    {
        Session::manager()->id('asdf')->start();
        $this->assertEquals(session_id(), 'asdf');
    }

    public function testSessionName()
    {
        Session::manager()->name('asdf')->start();
        $this->assertEquals(session_name(), 'asdf');
    }

    public function testSetter()
    {
        $this->sessionStart();
        Session::set('test', 'test1');
        Session::set('asdf', 'asdf321');

        $this->assertEquals($_SESSION['test'], 'test1');
        $this->assertEquals($_SESSION['asdf'], 'asdf321');
    }

    public function testGetter()
    {
        $this->sessionStart();
        Session::set('test', 'test1');
        Session::set('asdf', 'asdf321');

        $this->assertEquals(Session::get('test'), 'test1');
        $this->assertEquals(Session::get('asdf'), 'asdf321');
    }

    public function testHelpers()
    {
        $this->sessionStart();
        Session::set('aaa', [
            'bbb' => [
                'ccc' => 111,
                'ddd' => 222,
            ],
        ]);
        Session::set('bbb', 'mfmfmffm');
        Session::set('ccc', 'asdf321');

        $this->assertEquals(Session::get('aaa.bbb.ccc'), '111');

        $this->assertTrue(Session::has('bbb'));
        $this->assertFalse(Session::has('asdfasdfa'));

        $this->assertEquals(Session::only(['bbb', 'ccc']), ['bbb' => 'mfmfmffm', 'ccc' => 'asdf321']);
        $this->assertEquals(Session::except(['aaa', 'ccc']), ['bbb' => 'mfmfmffm']);

        Session::forget('aaa.bbb.ccc');
        $this->assertNull(Session::get('aaa.bbb.ccc'));
    }

    public function testCollect()
    {
        $this->sessionStart();
        Session::set('aaa', [
            [
                "name" => "bangi",
                "position" => "god",
            ],
            [
                "name" => "faker",
                "position" => "human",
            ],
            [
                "name" => "duke",
                "position" => "human",
            ],
            [
                "name" => "wolf",
                "position" => "pig",
            ],
        ]);

        $collect = Session::collect('aaa');

        $this->assertTrue($collect->contains('name', 'bangi'));
        $this->assertEquals($collect->where('position', 'human')->all(),
            [
                1 => [
                    "name" => "faker",
                    "position" => "human",
                ],
                2 => [
                    "name" => "duke",
                    "position" => "human",
                ],
            ]
        );
    }

}

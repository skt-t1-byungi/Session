<?php
/**
 * @author a-le
 * @link https://github.com/a-le/php-session-file-handler/blob/master/FileSessionHandler.php
 */
namespace SktT1Byungi\Session\Handler;

use SessionHandlerInterface;

class File implements SessionHandlerInterface
{
    private $savePath;

    private $data;

    public function __construct($savePath = null)
    {
        if (is_null($savePath)) {
            $savePath = session_save_path();
        } else {
            session_save_path($savePath);
        }

        $this->savePath = $savePath;

        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0755, true);
        }

        var_dump($this->savePath);
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        $this->data = false;
        $filename = $this->savePath . '/sess_' . $id;

        if (file_exists($filename)) {
            $this->data = @file_get_contents($filename);
        }

        if ($this->data === false) {
            $this->data = '';
        }

        return $this->data;
    }

    public function write($id, $data)
    {
        $filename = $this->savePath . '/sess_' . $id;

        if ($data !== $this->data) {
            return @file_put_contents($filename, $data, LOCK_EX) === false ? false : true;
        } else {
            return @touch($filename);
        }
    }

    public function destroy($id)
    {
        $filename = $this->savePath . '/sess_' . $id;

        if (file_exists($filename)) {
            @unlink($filename);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        foreach (glob($this->savePath . '/sess_*') as $filename) {

            if (filemtime($filename) + $maxlifetime < time() && file_exists($filename)) {
                @unlink($filename);
            }
        }
        return true;
    }
}

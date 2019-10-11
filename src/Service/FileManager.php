<?php


namespace App\Service;


class FileManager
{
    private $path;
    private $filename;
    private $content;

    /**
     * Write the log in a file
     * @param string $content
     */
    public function writeLog(string $content)
    {
        $this->path = getenv('APP_LOG_PATH');
        $this->filename = 'log-' . date('Ymd') . '.log';
        $this->content = $content;

        $this->write();
    }

    /**
     * Write in the file
     */
    private function write()
    {
        try {
            $file = $this->path . $this->filename;
            $resource = fopen($file, 'a');
            if(false !== $resource)
            {
                fwrite($resource, $this->content);
                fclose($resource);
            }

        } catch(\Exception $e) {
            // do nothing
        }
    }
}
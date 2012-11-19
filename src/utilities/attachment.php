<?php

include_once 'src/utilities/util.php';

class Attachment {
    protected $content;
    protected $size;
    protected $name;
    protected $extension;

    protected $isFile;

    /**
     * @param string  $name      Attachment file name
     * @param string  $extension Attachment file extension
     * @param string  $content   Attachment file content or the file path to the content
     * @param boolean $isFile    Specify the $content is real content or file path
     */
    public function __construct($name, $extension, $content, $isFile = false) {
        $this->name = $name;
        $this->extension = $extension;
        $this->content = $content;
        $this->isFile = $isFile;

        $this->size = $isFile? filesize($content) : strlen($content);
        if ($this->size <=0)
            Util::addError('Attachment content is empty.');
    }

    public function attach() {
        if ($this->size <= 0) return;

        header('Content-Description: File Transfer');
        header('Cache-Control: public');
        header('Content-Type: '.$this->extension);
        header("Content-Transfer-Encoding: binary");
        header('Content-Disposition: attachment; filename='. $this->name);
        header('Content-Length: '.$this->size);
        ob_clean(); #THIS!
        flush();

        if($this->isFile)
         readfile($this->content);
        else 
            echo $this->content;
    }
}
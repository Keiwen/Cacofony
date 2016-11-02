<?php
namespace Keiwen\Cacofony\Controller;


trait MessageManagerTrait
{

    private $messages = array();
    private $retrievedMessages = false;


    /**
     * list message types
     * @return array
     */
    public abstract function getMessageTypes(): array;

    /**
     * @return string
     */
    public abstract function getMessageDefaultType(): string;

    /**
     * store messages in memory
     */
    protected function storeMessages()
    {
        if(!$this->hasRetrievedMessages()) $this->retrieveMessages();
    }

    /**
     * retrieve messages from memory
     */
    protected function retrieveMessages()
    {
        $this->retrievedMessages = true;
    }

    /**
     * @return bool
     */
    protected function hasRetrievedMessages()
    {
        return $this->retrievedMessages;
    }


    /**
     * @param string $message
     * @param string $type
     */
    public function addMessage(string $message, string $type = '')
    {
        if(!$this->hasRetrievedMessages()) $this->retrieveMessages();
        if(!in_array($type, $this->getMessageTypes())) {
            $type = static::getMessageDefaultType();
        }
        $this->messages[] = array('type' => $type, 'message' => $message);
    }


    /**
     * @param string $type
     * @return array
     */
    public function getMessages(string $type = '')
    {
        if(!$this->hasRetrievedMessages()) $this->retrieveMessages();
        if(empty($type)) return $this->messages;
        $messages= array();
        foreach($this->messages as $msg) {
            if($msg['type'] == $type) $messages[] = $msg;
        }
        return $messages;
    }


    /**
     *
     */
    public function emptyMessages()
    {
        if(!$this->hasRetrievedMessages()) $this->retrieveMessages();
        $this->messages = array();
    }



}
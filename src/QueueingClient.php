<?php
namespace MeadSteve\Raygun4php;

use MeadSteve\Raygun4php\Senders\MessageSender;

class QueueingClient
{
    protected $useAsyncSending;
    protected $queuedMessages = array();

    /**
     * @var Senders\StreamSocketSender
     */
    protected $messageSender;

    /**
     * @var MessageBuilder
     */
    protected $messageBuilder;

    public function __construct(MessageBuilder $messageBuilder, MessageSender $messageSender, $useAsyncSending = true)
    {
        $this->useAsyncSending = $useAsyncSending;
        $this->messageSender = $messageSender;
        $this->messageBuilder = $messageBuilder;
    }

    /**
     * Transmits an error to the Raygun.io API
     * @param $errno
     * @param string $errstr The error string
     * @param string $errfile The file the error occurred in
     * @param int $errline The line the error occurred on
     * @param array $tags An optional array of string tags used to provide metadata for the message
     * @param array $userCustomData An optional associative array that can be used to place custom key-value
     * data in the message payload
     * @param mixed $timestamp
     * @internal param int $errorno The error number
     * @return int The HTTP status code of the result when transmitting the message to Raygun.io
     */
    public function SendError(
        $errno,
        $errstr,
        $errfile,
        $errline,
        $tags = null,
        $userCustomData = null,
        $timestamp = null
    ) {
        $errorException = new \ErrorException($errstr, $errno, 0, $errfile, $errline);
        $message = $this->messageBuilder->BuildMessage($errorException, $timestamp);

        if ($tags != null) {
            $this->messageBuilder->AddTagsToMessage($message, $tags);
        }
        if ($userCustomData != null) {
            $this->messageBuilder->AddUserCustomDataToMessage($message, $userCustomData);
        }

        return $this->Send($message);
    }

    /**
     * Transmits an exception to the Raygun.io API
     * @param \Exception $exception An exception object to transmit
     * @param array $tags An optional array of string tags used to provide metadata for the message
     * @param array $userCustomData An optional associative array that can be used to place custom key-value
     * data in the message payload
     * @param mixed $timestamp
     * @return int The HTTP status code of the result when transmitting the message to Raygun.io
     */
    public function SendException($exception, $tags = null, $userCustomData = null, $timestamp = null)
    {
        $message = $this->messageBuilder->BuildMessage($exception, $timestamp);

        if ($tags != null) {
            $this->messageBuilder->AddTagsToMessage($message, $tags);
        }
        if ($userCustomData != null) {
            $this->messageBuilder->AddUserCustomDataToMessage($message, $userCustomData);
        }

        return $this->Send($message);
    }

    /**
     * Sets the version number of your project that will be transmitted
     * to Raygun.io.
     * @param string $version The version number in the form of x.x.x.x,
     * where x is a positive integer.
     *
     */
    public function SetVersion($version)
    {
        $this->messageBuilder->SetVersion($version);
    }

    /**
    *  Stores the current user of the calling application. This will be added to any messages sent
    *  by this provider. It is used in the dashboard to provide unique user tracking.
    *  If it is an email address, the user's Gravatar can be displayed. This method is optional,
    *  if it is not used a random identifier will be assigned to the current user.
    *  @param string $user A username, email address or other identifier for the current user
    *  of the calling application.
    *
    */
    public function SetUser($user = null)
    {
        $this->messageBuilder->SetUser($user);
    }

    /**
     * Transmits an exception or ErrorException to the Raygun.io API. The default attempts to transmit asynchronously.
     * To disable this and transmit sync (blocking), pass false in as the 2nd parameter in RaygunClient's
     * constructor. This may be necessary on some Windows installations where the implementation is broken.
     * @param Message $message A populated message to be posted to the Raygun API
     * @return int The HTTP status code of the result after transmitting the message to Raygun.io
     * 202 if accepted, 403 if invalid JSON payload
     */
    public function Send($message)
    {
        if ($this->useAsyncSending) {
            return $this->queueMessage($message);
        } else {
            return $this->messageSender->Send($message);
        }
    }

    public function flushSendQueue()
    {
        foreach ($this->queuedMessages as $message) {
            try {
                $this->messageSender->Send($message);
            } catch (\Exception $exception) {
                syslog(LOG_ERR, "Failed to record error with Raygun because " . $exception->getMessage());
            }
        }
        $this->queuedMessages = array();
    }

    private function queueMessage($data_to_send)
    {
        $this->queuedMessages[] = $data_to_send;
        return 202;
    }

    public function __destruct()
    {
        $this->flushSendQueue();
    }
}

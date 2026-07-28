<?php

namespace SofteriaTech\SmsPro\Channels;

use Illuminate\Notifications\Notification;
use SofteriaTech\SmsPro\SmsPro;
use SofteriaTech\SmsPro\SmsProMessage;
use SofteriaTech\SmsPro\Exceptions\SmsProException;

class SmsProChannel
{
    /**
     * @var SmsPro
     */
    protected $smsPro;

    /**
     * Constructor
     *
     * @param SmsPro $smsPro
     */
    public function __construct(SmsPro $smsPro)
    {
        $this->smsPro = $smsPro;
    }

    /**
     * Send the notification
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return array|null
     * @throws SmsProException
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toSmsPro')) {
            throw new SmsProException('Notification must implement toSmsPro method');
        }

        $message = $notification->toSmsPro($notifiable);

        if (! $message instanceof SmsProMessage) {
            throw new SmsProException('toSmsPro must return an instance of SmsProMessage');
        }

        // Get the mobile number
        $mobile = $this->getMobile($notifiable, $message);
        
        if (empty($mobile)) {
            throw new SmsProException('No mobile number found for notification');
        }

        // If message has a group ID, send to group
        if ($message->getGroupId()) {
            return $this->smsPro->sendToGroup(
                $message->getGroupId(),
                $message->getContent(),
                $message->getSenderId()
            );
        }

        // Send SMS
        return $this->smsPro->send(
            $mobile,
            $message->getContent(),
            $message->getSenderId()
        );
    }

    /**
     * Get the mobile number from the notifiable
     *
     * @param mixed $notifiable
     * @param SmsProMessage $message
     * @return string|null
     */
    protected function getMobile($notifiable, SmsProMessage $message): ?string
    {
        // If mobile is explicitly set in the message
        if ($message->getMobile()) {
            return $message->getMobile();
        }

        // If notifiable has routeNotificationForSmsPro method
        if (method_exists($notifiable, 'routeNotificationForSmsPro')) {
            return $notifiable->routeNotificationForSmsPro();
        }

        // If notifiable has routeNotificationFor method
        if (method_exists($notifiable, 'routeNotificationFor')) {
            return $notifiable->routeNotificationFor('smspro');
        }

        // If notifiable has phone number attribute
        if (isset($notifiable->phone)) {
            return $notifiable->phone;
        }

        // If notifiable has mobile attribute
        if (isset($notifiable->mobile)) {
            return $notifiable->mobile;
        }

        // If notifiable has phone_number attribute
        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        return null;
    }
}
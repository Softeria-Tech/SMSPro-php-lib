<?php

namespace SofteriaTech\SmsPro;

class SmsProMessage
{
    /**
     * @var string|null
     */
    protected $mobile;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string|null
     */
    protected $senderId;

    /**
     * @var string|null
     */
    protected $groupId;

    /**
     * Create a new message instance
     *
     * @param string $content
     */
    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * Create a new message instance
     *
     * @param string $content
     * @return static
     */
    public static function create(string $content = ''): self
    {
        return new static($content);
    }

    /**
     * Set the mobile number
     *
     * @param string $mobile
     * @return $this
     */
    public function to(string $mobile): self
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * Set the message content
     *
     * @param string $content
     * @return $this
     */
    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the sender ID
     *
     * @param string $senderId
     * @return $this
     */
    public function from(string $senderId): self
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * Send to a group
     *
     * @param string $groupId
     * @return $this
     */
    public function toGroup(string $groupId): self
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Get the mobile number
     *
     * @return string|null
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * Get the message content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get the sender ID
     *
     * @return string|null
     */
    public function getSenderId(): ?string
    {
        return $this->senderId;
    }

    /**
     * Get the group ID
     *
     * @return string|null
     */
    public function getGroupId(): ?string
    {
        return $this->groupId;
    }
}
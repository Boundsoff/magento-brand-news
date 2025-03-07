<?php

namespace Boundsoff\BrandNews\Model;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Magento\AdminNotification\Model\InboxFactory;

class FeedbackService implements FeedbackServiceInterface
{
    /**
     * @param InboxFactory $inboxFactory
     */
    public function __construct(
        protected readonly InboxFactory $inboxFactory,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function add(string $title, string $description, string $url = ''): void
    {
        $this->inboxFactory->create()
            ->addNotice($title, $description, $url);
    }
}

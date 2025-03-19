<?php

namespace Boundsoff\BrandNews\Observer;

use Boundsoff\BrandNews\Api\FeedbackServiceInterface;
use Boundsoff\BrandNews\Model\Exception\FeedbackServiceException;
use DateMalformedStringException;
use Magento\Backend\Model\Auth\Session as BackendAuthSession;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class CheckFeedbackObserver implements ObserverInterface
{
    /**
     * @param BackendAuthSession $backendAuthSession
     * @param FeedbackServiceInterface $feedbackService
     * @param DataObject $dataObject
     */
    public function __construct(
        protected readonly BackendAuthSession $backendAuthSession,
        protected readonly FeedbackServiceInterface $feedbackService,
        protected readonly DataObject         $dataObject,
    ) {
    }

    /**
     * Check for feedback data to be appended to notifications
     *
     * @param Observer $observer
     *
     * @return void
     * @throws FeedbackServiceException
     * @throws DateMalformedStringException
     */
    public function execute(Observer $observer)
    {
        if (!$this->backendAuthSession->isLoggedIn()) {
            return;
        }

        foreach ($this->getItems() as $item) {
            $title = $item->getData('title');
            $description = $item->getData('description');
            $url = $item->getData('url');
            $timeout = $item->getData('timeout');

            $this->feedbackService->add($title, $description, $url, $timeout);
        }
    }

    /**
     * Helper method to get items for feedback
     *
     * @return DataObject[]
     */
    protected function getItems(): array
    {
        $items = $this->dataObject->getData() ?? [];
        return array_map(fn($item) => new DataObject($item), $items);
    }
}

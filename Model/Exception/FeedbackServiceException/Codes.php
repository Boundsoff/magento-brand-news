<?php

namespace Boundsoff\BrandNews\Model\Exception\FeedbackServiceException;

use Boundsoff\BrandNews\Model\Exception\FeedbackServiceException;

enum Codes: int
{
    case EmptyTitle = 0;
    case EmptyDescription = 1;
    case EmptyUrl = 2;
    case InvalidUrl = 3;
    case ResponseCodeInvalid = 4;

    /**
     * Get exception class to be thrown
     *
     * @param array $context
     * @return FeedbackServiceException
     */
    public function getException(array $context = []): FeedbackServiceException
    {
        return (match ($this) {
            self::EmptyTitle => new FeedbackServiceException(__('Title is empty.'), code: $this->value),
            self::EmptyDescription => new FeedbackServiceException(__('Description is empty.'), code: $this->value),
            self::EmptyUrl => new FeedbackServiceException(__('Url is empty.'), code: $this->value),
            self::InvalidUrl => new FeedbackServiceException(__('Invalid url.'), code: $this->value),
            self::ResponseCodeInvalid => new FeedbackServiceException(
                __('Invalid response code.'),
                code: $this->value,
            ),
        })
            ->setContext($context);
    }
}

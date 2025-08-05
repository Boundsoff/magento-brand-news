<?php

namespace Boundsoff\BrandNews\Model\Exception;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

abstract class AbstractDataLocalizeException extends LocalizedException
{
    /** @var DataObject  */
    protected readonly DataObject $context;

    /**
     * @param Phrase $phrase
     * @param Exception|null $cause
     * @param int $code
     */
    public function __construct(
        Phrase     $phrase,
        ?Exception $cause = null,
        int        $code = 0,
    ) {
        parent::__construct($phrase, $cause, $code);
        $this->context = new DataObject();
    }

    /**
     * Set additional information about thrown exception
     *
     * @param array $context
     * @return $this
     */
    public function setContext(array $context = []): self
    {
        $this->context->setData($context);
        return $this;
    }
}

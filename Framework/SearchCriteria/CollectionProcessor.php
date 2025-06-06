<?php
/**
 * Cytracon
 *
 * This source file is subject to the Cytracon Software License, which is available at https://www.cytracon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.cytracon.com for more information.
 *
 * @category  Cytracon
 * @package   Cytracon_Core
 * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
 */

namespace Cytracon\Core\Framework\SearchCriteria;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class CollectionProcessor implements CollectionProcessorInterface
{
    /**
     * @var CollectionProcessorInterface[]
     */
    private $processors;

    /**
     * @param CollectionProcessorInterface[] $processors
     */
    public function __construct(
        array $processors
    ) {
        $this->processors = $processors;
    }

    /**
     * @inheritDoc
     */
    public function process(SearchCriteriaInterface $searchCriteria, AbstractDb $collection)
    {
        foreach ($this->processors as $name => $processor) {
            if (!($processor instanceof CollectionProcessorInterface)) {
                throw new \InvalidArgumentException(
                    sprintf('Processor %s must implement %s interface.', $name, CollectionProcessorInterface::class)
                );
            }
            $processor->process($searchCriteria, $collection);
        }
    }
}

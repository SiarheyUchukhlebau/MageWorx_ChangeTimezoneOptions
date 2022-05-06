<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ChangeTimezoneOptions\Plugin;

/**
 * Class UpdateTimezoneLabels
 */
class UpdateTimezoneLabels
{
    /**
     * @param \Magento\Framework\Locale\ListsInterface $subject
     * @param array $options
     * @return array
     */
    public function afterGetOptionTimezones(\Magento\Framework\Locale\ListsInterface $subject, array $options) {
        foreach ($options as &$option) {
            if (empty($option['value'])) {
                continue;
            }

            $option['offset'] = $this->getOffsetByCode($option['value']);
        }

        return $this->_sortOptionArray($options);
    }

    /**
     * @param array $option
     * @return array
     */
    protected function _sortOptionArray(array $option): array
    {
        $offsets = [];
        foreach ($option as $key => $item) {
            $offset = (string)$item['offset'];
            $offsets[$offset]['label'] = $offset;
            $offsets[$offset]['value'][] = [
                'value' => $item['value'],
                'label' => $item['label']
            ];
        }

        asort($offsets);

        return $offsets;
    }

    /**
     * Get time offset by timezone code
     *
     * @param string $code
     * @return float
     */
    protected function getOffsetByCode(string $code): float
    {
        return \IntlTimeZone::createTimeZone($code)->getRawOffset() / (1000 * 60 * 60);
    }
}

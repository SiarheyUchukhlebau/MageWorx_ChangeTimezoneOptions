<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ChangeTimezoneOptions\Plugin;


/**
 * Class UpdateTimezoneLabels
 */
class UpdateTimezoneLabels
{
    /**
     * @param \Magento\Framework\Locale\ListsInterface $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundGetOptionTimezones(\Magento\Framework\Locale\ListsInterface $subject, \Closure $proceed) {
        $options = $proceed();

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
    protected function _sortOptionArray($option)
    {
        $offsets = [];
        foreach ($option as $key => $item) {
            $offsets[$item['offset']]['label'] = $item['offset'];
            $offsets[$item['offset']]['value'][] = [
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
     * @return float|int
     */
    protected function getOffsetByCode(string $code)
    {
        return \IntlTimeZone::createTimeZone($code)->getRawOffset() / (1000 * 60 * 60);
    }
}
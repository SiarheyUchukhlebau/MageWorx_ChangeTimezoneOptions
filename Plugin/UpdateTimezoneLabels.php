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
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * UpdateTimezoneLabels constructor.
     *
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->localeResolver = $localeResolver;
    }

    public function aroundGetOptionTimezones($subject, $proceed) {
        $options = [];
        $locale = $this->localeResolver->getLocale();
        $zones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL) ?: [];
        foreach ($zones as $code) {
            $offset = \IntlTimeZone::createTimeZone($code)->getRawOffset() / (1000 * 60 * 60);
            $options[] = [
                'label' => \IntlTimeZone::createTimeZone($code)
                                        ->getDisplayName(false, \IntlTimeZone::DISPLAY_LONG, $locale) .
                    ' (' . $code . ') ' .
                     ' [' . $offset . ']'
                    ,
                'value' => $code,
                'offset' => $offset
            ];
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
}
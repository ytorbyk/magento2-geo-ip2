<?php
/**
 * Copyright © 2015 ToBai. All rights reserved.
 */

namespace Tobai\GeoIp2\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class Update
 */
class Update extends Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();
        $element->unsCanUseWebsiteValue();
        $element->unsCanUseDefaultValue();
        $element->setValue(__('Update'));
        $element->setData('onclick', 'javascript:geoIpUpdate(); return false;');
        $element->setData('class', 'action-default');

        $element->setData('after_element_js', '<script type="application/javascript">
    require(["jquery"], function($){
        window.geoIpUpdate = function() {
            $.getJSON(
                "' . $this->getUrl('tobai_geoip2/database/update') . '",
                function(data) {
                    if (data.status_info) {
                        $("#row_tobai_geoip2_database_status .value").html(data.status_info);
                    }
                }
            );
        };

        $(document).bind("ajaxSend", function() {
            $("body").trigger("processStart");
        });

        $(document).bind("ajaxComplete", function() {
            $("body").trigger("processStop");
        });
    });
</script>');

        return parent::render($element);
    }
}

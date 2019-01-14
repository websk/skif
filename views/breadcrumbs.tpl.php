<?php
/**
 * @var BreadcrumbItemDTO[] $breadcrumbs_dto_arr
 */

use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Utils\Assert;
use WebSK\Utils\Sanitize;

?>
<ol class="breadcrumb">
    <?php
    foreach ($breadcrumbs_dto_arr as $breadcrumb_item_dto) {
        Assert::assert($breadcrumb_item_dto instanceof BreadcrumbItemDTO);
        if (!$breadcrumb_item_dto->getUrl()) {
            echo '<li class="active">' . $breadcrumb_item_dto->getName() . '</li>';
            continue;
        }

        echo '<li><a href="' . Sanitize::sanitizeUrl($breadcrumb_item_dto->getUrl()) . '">' . Sanitize::sanitizeTagContent($breadcrumb_item_dto->getName()) . '</a></li>';
    }
    ?>
</ol>

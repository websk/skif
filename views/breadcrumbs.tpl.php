<?php
/**
 * @var BreadcrumbItemDTO[] $breadcrumbs_dto_arr
 */

use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Skif\TemplateSanitize;
use WebSK\Utils\Assert;
use WebSK\Utils\Sanitize;

if (!isset($breadcrumbs_dto_arr)) {
    $breadcrumbs_dto_arr = [];
}
?>
<ol class="breadcrumb mb-0">
    <?php
    foreach ($breadcrumbs_dto_arr as $breadcrumb_item_dto) {
        Assert::assert($breadcrumb_item_dto instanceof BreadcrumbItemDTO);
        if (!$breadcrumb_item_dto->getUrl()) {
            echo '<li class="breadcrumb-item active" aria-current="page">' . Sanitize::sanitizeTagContent($breadcrumb_item_dto->getName()) . '</li>';
            continue;
        }

        echo '<li class="breadcrumb-item"><a href="' . TemplateSanitize::urlAttribute($breadcrumb_item_dto->getUrl()) . '">' . Sanitize::sanitizeTagContent($breadcrumb_item_dto->getName()) . '</a></li>';
    }
    ?>
</ol>

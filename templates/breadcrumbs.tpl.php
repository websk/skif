<?php
/**
 * @var $breadcrumbs_arr
 */

?>
<ol class="breadcrumb">
    <?php
    foreach ($breadcrumbs_arr as $breadcrumb_title => $breadcrumb_link) {
        if (empty($breadcrumb_link)) {
            echo '<li class="active">' . $breadcrumb_title . '</li>';
        } else {
            echo '<li><a href="' . $breadcrumb_link . '">' . $breadcrumb_title . '</a></li>';
        }
    }
    ?>
</ol>

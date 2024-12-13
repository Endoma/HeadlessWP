<?php
if (!is_user_logged_in()) {
    $url = get_option('headlesswp_api_url', '');
    header( "Location:" . $url);
}
else {
    echo '<a href="/wp-admin" class="button">Go to Admin</a>';
}
<?php
if (!is_user_logged_in()) {
    header( "Location: http://localhost:3000/" );
}
else {
    echo '<a href="/wp-admin" class="button">Go to Admin</a>';
}
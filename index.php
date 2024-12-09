<?php
if (!is_user_logged_in()) {
    header( "Location: http://localhost:3000/" );
}
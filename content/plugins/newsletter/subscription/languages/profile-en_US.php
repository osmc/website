<?php

// This file is used only on first installation!

$options = array();
$options['email'] = 'Email';
$options['email_error'] = 'The email is not correct';
$options['name'] = 'Name';
$options['name_error'] = 'The name is not correct';
$options['name_status'] = 0;
$options['name_rules'] = 0;
$options['surname'] = 'Last name';
$options['surname_error'] = 'The last name is not correct';
$options['surname_status'] = 0;
$options['sex'] = 'I\'m';
$options['privacy'] = 'Subscribing I accept the privacy rules of this site';
$options['privacy_error'] = 'You must accept the privacy statement';
$options['privacy_status'] = 0;
$options['subscribe'] = 'Subscribe';
$options['save'] = 'Save';

$options['title_female'] = 'Mrs.';
$options['title_male'] = 'Mr.';
$options['title_none'] = 'Dear';

$options['sex_male'] = 'Man';
$options['sex_female'] = 'Woman';
$options['sex_none'] = 'None';

for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    $options['list_' . $i . '_status'] = 0;
}

for ($i=1; $i<=NEWSLETTER_PROFILE_MAX; $i++) {
    $options['profile_' . $i . '_status'] = 0;
}

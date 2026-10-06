<?php

test('guest users are redirected to login', function () {
    $this->get('/')
        ->assertRedirect('/login');
});
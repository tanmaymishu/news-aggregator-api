<?php

test('the homepage redirects to the /docs/api page', function () {
    $response = $this->get('/');

    $response->assertStatus(302)->assertRedirect('/docs/api');
});

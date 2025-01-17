<?php

use App\Models\User;

test('team members can be removed from teams', function () {
    $this->actingAs($user = User::factory()->withPersonalTeam()->create());

    $user->currentTeam->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $response = $this->delete('/teams/'.$user->currentTeam->{$user->currentTeam->getKeyName()}.'/members/'.$otherUser->{$otherUser->getKeyName()});

    expect($user->currentTeam->fresh()->users)->toHaveCount(0);
});

test('only team owner can remove team members', function () {
    $user = User::factory()->withPersonalTeam()->create();

    $user->currentTeam->users()->attach(
        $otherUser = User::factory()->create(), ['role' => 'admin']
    );

    $this->actingAs($otherUser);

    $response = $this->delete('/teams/'.$user->currentTeam->{$user->currentTeam->getKeyName()}.'/members/'.$user->{$user->getKeyName()});

    $response->assertStatus(403);
});

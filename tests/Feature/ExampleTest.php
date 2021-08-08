<?php

use App\Models\Series;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('is creates a visit', function () {
    $series = Series::factory()->create();

    $series->visit();

    expect($series->visits->count())->toBe(1);
});

it('is creates a visit with the default ip address', function () {
    $series = Series::factory()->create();

    $series->visit()->withIp();

    expect($series->visits->first()->data)->toMatchArray(['ip' => request()->ip()]);
});

it('is creates a visit with the given ip address', function () {
    $series = Series::factory()->create();

    $series->visit()->withIp('cats');

    expect($series->visits->first()->data)->toMatchArray(['ip' => 'cats']);
});

it('is creates a visit with custom data', function () {
    $series = Series::factory()->create();

    $series->visit()->withData([
        'cats' => true
    ]);

    expect($series->visits->first()->data)->toMatchArray(['cats' => true]);
});

it('is creates a visit with the default user', function () {
    $this->actingAs($user = User::factory()->create());
    $series = Series::factory()->create();

    $series->visit()->withUser();

    expect($series->visits->first()->data)->toMatchArray(['user_id' => $user->id]);
});

it('is creates a visit with a given user', function () {
    $user = User::factory()->create();
    $series = Series::factory()->create();

    $series->visit()->withUser($user);

    expect($series->visits->first()->data)->toMatchArray(['user_id' => $user->id]);
});

it('is does not create duplicate visits with the same data', function () {
    $series = Series::factory()->create();

    $series->visit()->withData([
        'cats' => true
    ]);

    $series->visit()->withData([
        'cats' => true
    ]);

    expect($series->visits->count())->toBe(1);
});

it('is does not create visits within the timeframe', function () {
    $series = Series::factory()->create();

    Carbon::setTestNow(now()->subDays(2));
    $series->visit();

    Carbon::setTestNow();
    $series->visit();
    $series->visit();

    expect($series->visits->count())->toBe(2);
});

it('is creates visits after a hourly timeframe', function () {
    $series = Series::factory()->create();

    $series->visit()->withIp();
    Carbon::setTestNow(now()->addDay()->addHour());
    $series->visit()->withIp();

    expect($series->visits->count())->toBe(2);
});

it('is creates visits after a daily timeframe', function () {
    $series = Series::factory()->create();

    $series->visit()->dailyIntervals()->withIp();
    Carbon::setTestNow(now()->addDay()->addHour());
    $series->visit()->dailyIntervals()->withIp();

    expect($series->visits->count())->toBe(2);
});

it('is creates visits after a weekly timeframe', function () {
    $series = Series::factory()->create();

    $series->visit()->weeklyIntervals()->withIp();
    Carbon::setTestNow(now()->addWeek());
    $series->visit()->weeklyIntervals()->withIp();

    expect($series->visits->count())->toBe(2);
});

it('is creates visits after a monthly timeframe', function () {
    $series = Series::factory()->create();

    $series->visit()->monthlyIntervals()->withIp();
    Carbon::setTestNow(now()->addMonth());
    $series->visit()->monthlyIntervals()->withIp();

    expect($series->visits->count())->toBe(2);
});

it('is creates visits after a custom timeframe', function () {
    $series = Series::factory()->create();

    $series->visit()->customIntervals(now()->subYear())->withIp();
    Carbon::setTestNow(now()->addYear());
    $series->visit()->customIntervals(now()->subYear())->withIp();

    expect($series->visits->count())->toBe(2);
});



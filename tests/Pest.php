<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class)->in('Feature');
uses(TestCase::class)->in('Unit');

uses(RefreshDatabase::class)->in('Feature');


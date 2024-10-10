<?php

namespace Darinlarimore\StatamicImagehotspots\Tests;

use Darinlarimore\StatamicImagehotspots\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}

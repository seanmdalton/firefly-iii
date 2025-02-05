<?php
/**
 * RecurringCronjobTest.php
 * Copyright (c) 2019 thegrumpydictator@gmail.com
 *
 * This file is part of Firefly III.
 *
 * Firefly III is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Firefly III is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Firefly III. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tests\Unit\Support\Cronjobs;


use Carbon\Carbon;
use FireflyConfig;
use FireflyIII\Jobs\CreateRecurringTransactions;
use FireflyIII\Models\Configuration;
use FireflyIII\Support\Cronjobs\RecurringCronjob;
use Log;
use Mockery;
use Tests\TestCase;

/**
 * Class RecurringCronjobTest
 */
class RecurringCronjobTest extends TestCase
{
    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        Log::info(sprintf('Now in %s.', get_class($this)));
    }

    /**
     * @covers \FireflyIII\Support\Cronjobs\RecurringCronjob
     */
    public function testBasic(): void
    {
        $class        = $this->mock(CreateRecurringTransactions::class);
        $force        = false;
        $date         = new Carbon;
        $config       = new Configuration;
        $config->data = 0;
        FireflyConfig::shouldReceive('get')->withArgs(['last_rt_job', 0])->atLeast()->once()->andReturn($config);

        $class->shouldReceive('setDate')->atleast()->once();
        $class->shouldReceive('setForce')->atleast()->once()->withArgs([$force]);
        $class->shouldReceive('handle')->atleast()->once();

        FireflyConfig::shouldReceive('set')->atLeast()->once()->withArgs(['last_rt_job', Mockery::any()]);

        $job = new RecurringCronjob;
        $job->setDate($date);
        $job->setForce($force);
        $job->fire();
    }

    /**
     * @covers \FireflyIII\Support\Cronjobs\RecurringCronjob
     */
    public function testShort(): void
    {
        $this->mock(CreateRecurringTransactions::class);
        $force        = false;
        $date         = new Carbon;
        $time         = time() - 100;
        $config       = new Configuration;
        $config->data = $time;

        FireflyConfig::shouldReceive('get')->withArgs(['last_rt_job', 0])->atLeast()->once()->andReturn($config);

        $job = new RecurringCronjob;
        $job->setDate($date);
        $job->setForce($force);
        $job->fire();
    }

    /**
     * @covers \FireflyIII\Support\Cronjobs\RecurringCronjob
     */
    public function testShortForced(): void
    {
        $class        = $this->mock(CreateRecurringTransactions::class);
        $force        = true;
        $date         = new Carbon;
        $time         = time() - 100;
        $config       = new Configuration;
        $config->data = $time;
        FireflyConfig::shouldReceive('get')->withArgs(['last_rt_job', 0])->atLeast()->once()->andReturn($config);

        $class->shouldReceive('setDate')->atleast()->once();
        $class->shouldReceive('setForce')->atleast()->once()->withArgs([$force]);
        $class->shouldReceive('handle')->atleast()->once();

        FireflyConfig::shouldReceive('set')->atLeast()->once()->withArgs(['last_rt_job', Mockery::any()]);

        $job = new RecurringCronjob;
        $job->setDate($date);
        $job->setForce($force);
        $job->fire();
    }

    /**
     * @covers \FireflyIII\Support\Cronjobs\RecurringCronjob
     */
    public function testTwoDays(): void
    {
        $class        = $this->mock(CreateRecurringTransactions::class);
        $force        = false;
        $date         = new Carbon;
        $time         = time() - 43300;
        $config       = new Configuration;
        $config->data = $time;
        FireflyConfig::shouldReceive('get')->withArgs(['last_rt_job', 0])->atLeast()->once()->andReturn($config);

        $class->shouldReceive('setDate')->atleast()->once();
        $class->shouldReceive('setForce')->atleast()->once()->withArgs([$force]);
        $class->shouldReceive('handle')->atleast()->once();

        FireflyConfig::shouldReceive('set')->atLeast()->once()->withArgs(['last_rt_job', Mockery::any()]);

        $job = new RecurringCronjob;
        $job->setDate($date);
        $job->setForce($force);
        $job->fire();
    }

}
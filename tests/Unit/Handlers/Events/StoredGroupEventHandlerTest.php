<?php
/**
 * StoredGroupEventHandlerTest.php
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

declare(strict_types=1);

namespace Tests\Unit\Handlers\Events;


use FireflyIII\Events\StoredTransactionGroup;
use FireflyIII\Handlers\Events\StoredGroupEventHandler;
use FireflyIII\TransactionRules\Engine\RuleEngine;
use Log;
use Tests\TestCase;

/**
 *
 * Class StoredGroupEventHandlerTest
 */
class StoredGroupEventHandlerTest extends TestCase
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
     * @covers \FireflyIII\Handlers\Events\StoredGroupEventHandler
     */
    public function testProcessRules(): void
    {
        $group      = $this->getRandomWithdrawalGroup();
        $ruleEngine = $this->mock(RuleEngine::class);

        $ruleEngine->shouldReceive('setUser')->atLeast()->once();
        $ruleEngine->shouldReceive('setAllRules')->atLeast()->once()->withArgs([true]);
        $ruleEngine->shouldReceive('setTriggerMode')->atLeast()->once()->withArgs([RuleEngine::TRIGGER_STORE]);
        $ruleEngine->shouldReceive('processTransactionJournal')->atLeast()->once();

        $event = new StoredTransactionGroup($group, true);
        $handler = new StoredGroupEventHandler;
        $handler->processRules($event);
    }

    /**
     * @covers \FireflyIII\Handlers\Events\StoredGroupEventHandler
     */
    public function testNoProcessRules(): void
    {
        $group      = $this->getRandomWithdrawalGroup();
        $this->mock(RuleEngine::class);

        $event = new StoredTransactionGroup($group, false);
        $handler = new StoredGroupEventHandler;
        $handler->processRules($event);
        $this->assertTrue(true);

    }
}

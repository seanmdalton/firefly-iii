<?php
/**
 * ReportSumTest.php
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

namespace Tests\Unit\Console\Commands\Integrity;


use FireflyIII\Models\Transaction;
use FireflyIII\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Log;
use Tests\TestCase;

/**
 * Class ReportSumTest
 */
class ReportSumTest extends TestCase
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
     * @covers \FireflyIII\Console\Commands\Integrity\ReportSum
     */
    public function testHandle(): void
    {
        $repository = $this->mock(UserRepositoryInterface::class);
        $repository->shouldReceive('all')->atLeast()->once()->andReturn(new Collection([$this->user()]));

        $this->artisan('firefly-iii:report-sum')
             ->expectsOutput(sprintf('Amount integrity OK for user #%d', $this->user()->id))
             ->assertExitCode(0);

        // this method changes no objects so there is nothing to verify.
    }

    /**
     * Create transaction to make balance uneven.
     * @covers \FireflyIII\Console\Commands\Integrity\ReportSum
     */
    public function testHandleUneven(): void
    {
        $transaction = Transaction::create(
            [
                'transaction_journal_id' => $this->getRandomWithdrawal()->id,
                'user_id'                => 1,
                'account_id'             => $this->getRandomAsset()->id,
                'amount'                 => 10,
            ]
        );

        $repository = $this->mock(UserRepositoryInterface::class);
        $repository->shouldReceive('all')->atLeast()->once()->andReturn(new Collection([$this->user()]));

        $this->artisan('firefly-iii:report-sum')
             ->expectsOutput(sprintf('Error: Transactions for user #%d (%s) are off by %s!', $this->user()->id, $this->user()->email, '10.0'))
             ->assertExitCode(0);
        $transaction->forceDelete();

        // this method changes no objects so there is nothing to verify.
    }
}
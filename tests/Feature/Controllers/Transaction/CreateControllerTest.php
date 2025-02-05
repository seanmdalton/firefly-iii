<?php
/**
 * CreateControllerTest.php
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

namespace Tests\Feature\Controllers\Transaction;


use FireflyIII\Models\Preference;
use FireflyIII\Repositories\Account\AccountRepositoryInterface;
use FireflyIII\Repositories\User\UserRepositoryInterface;
use Log;
use Mockery;
use Preferences;
use Tests\TestCase;

/**
 * Class CreateControllerTest
 */
class CreateControllerTest extends TestCase
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
     * @covers \FireflyIII\Http\Controllers\Transaction\CreateController
     */
    public function testCreate(): void
    {
        $this->mockDefaultSession();
        $this->mockIntroPreference('shown_demo_transactions_create_withdrawal');
        $accountRepos = $this->mock(AccountRepositoryInterface::class);
        $userRepos    = $this->mock(UserRepositoryInterface::class);
        $cash         = $this->getRandomAsset();
        $empty        = new Preference;
        $empty->data  = [];

        $userRepos->shouldReceive('hasRole')->atLeast()->once()->withArgs([Mockery::any(), 'owner'])->andReturn(true);
        $accountRepos->shouldReceive('getCashAccount')->atLeast()->once()->andReturn($cash);

        Preferences::shouldReceive('get')->withArgs(['transaction_journal_optional_fields', []])->atLeast()->once()->andReturn($empty);


        $this->be($this->user());
        $response = $this->get(route('transactions.create', ['withdrawal']));
        $response->assertStatus(200);
    }
}
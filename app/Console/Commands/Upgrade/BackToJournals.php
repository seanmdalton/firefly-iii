<?php
/**
 * BackToJournals.php
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

namespace FireflyIII\Console\Commands\Upgrade;

use DB;
use FireflyIII\Models\Budget;
use FireflyIII\Models\Category;
use FireflyIII\Models\Transaction;
use FireflyIII\Models\TransactionJournal;
use Illuminate\Console\Command;

/**
 * Class BackToJournals
 */
class BackToJournals extends Command
{
    public const CONFIG_NAME = '4780_back_to_journals';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move meta data back to journals, not individual transactions.';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firefly-iii:back-to-journals {--F|force : Force the execution of this command.}';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // @codeCoverageIgnoreStart
        $start = microtime(true);
        if (!$this->isMigrated()) {
            $this->error('Please run firefly-iii:migrate-to-groups first.');
        }
        if ($this->isExecuted() && true !== $this->option('force')) {
            $this->info('This command has already been executed.');

            return 0;
        }
        if (true === $this->option('force')) {
            $this->warn('Forcing the command.');
        }
        // @codeCoverageIgnoreEnd

        $this->migrateAll();
        $end = round(microtime(true) - $start, 2);
        $this->info(sprintf('Updated category and budget info for all transaction journals in %s seconds.', $end));
        $this->markAsExecuted();

        return 0;
    }

    /**
     * @return array
     */
    private function getIdsForBudgets(): array
    {
        $transactions = DB::table('budget_transaction')->distinct()->get(['transaction_id'])->pluck('transaction_id')->toArray();

        return DB::table('transactions')
                 ->whereIn('transactions.id', $transactions)
                 ->get(['transaction_journal_id'])->pluck('transaction_journal_id')->toArray();
    }

    /**
     * @return array
     */
    private function getIdsForCategories(): array
    {
        $transactions = DB::table('category_transaction')->distinct()->get(['transaction_id'])->pluck('transaction_id')->toArray();

        return DB::table('transactions')
                 ->whereIn('transactions.id', $transactions)
                 ->get(['transaction_journal_id'])->pluck('transaction_journal_id')->toArray();
    }

    /**
     * @return bool
     */
    private function isExecuted(): bool
    {
        $configVar = app('fireflyconfig')->get(self::CONFIG_NAME, false);
        if (null !== $configVar) {
            return (bool)$configVar->data;
        }

        return false; // @codeCoverageIgnore
    }

    /**
     * @return bool
     */
    private function isMigrated(): bool
    {
        $configVar = app('fireflyconfig')->get(MigrateToGroups::CONFIG_NAME, false);
        if (null !== $configVar) {
            return (bool)$configVar->data;
        }

        return false; // @codeCoverageIgnore
    }

    /**
     *
     */
    private function markAsExecuted(): void
    {
        app('fireflyconfig')->set(self::CONFIG_NAME, true);
    }

    /**
     *
     */
    private function migrateAll(): void
    {
        $this->migrateBudgets();
        $this->migrateCategories();

        // empty tables
        DB::table('budget_transaction')->delete();
        DB::table('category_transaction')->delete();
    }

    /**
     *
     */
    private function migrateBudgets(): void
    {

        $journalIds = $this->getIdsForBudgets();
        $journals   = TransactionJournal::whereIn('id', $journalIds)->with(['transactions', 'budgets', 'transactions.budgets'])->get();
        $this->line(sprintf('Check %d transaction journal(s) for budget info.', count($journals)));
        /** @var TransactionJournal $journal */
        foreach ($journals as $journal) {
            $this->migrateBudgetsForJournal($journal);
        }
    }

    /**
     * @param TransactionJournal $journal
     */
    private function migrateBudgetsForJournal(TransactionJournal $journal): void
    {

        // grab category from first transaction
        /** @var Transaction $transaction */
        $transaction = $journal->transactions->first();
        if (null === $transaction) {
            // @codeCoverageIgnoreStart
            $this->info(sprintf('Transaction journal #%d has no transactions. Will be fixed later.', $journal->id));

            return;
            // @codeCoverageIgnoreEnd
        }
        /** @var Budget $budget */
        $budget = $transaction->budgets->first();
        /** @var Budget $journalBudget */
        $journalBudget = $journal->budgets->first();

        // both have a budget, but they don't match.
        if (null !== $budget && null !== $journalBudget && $budget->id !== $journalBudget->id) {
            // sync to journal:
            $journal->budgets()->sync([(int)$budget->id]);

            return;
        }

        // transaction has a budget, but the journal doesn't.
        if (null !== $budget && null === $journalBudget) {
            // sync to journal:
            $journal->budgets()->sync([(int)$budget->id]);
        }
    }

    /**
     *
     */
    private function migrateCategories(): void
    {
        $journalIds = $this->getIdsForCategories();
        $journals   = TransactionJournal::whereIn('id', $journalIds)->with(['transactions', 'categories', 'transactions.categories'])->get();
        $this->line(sprintf('Check %d transaction journal(s) for category info.', count($journals)));
        /** @var TransactionJournal $journal */
        foreach ($journals as $journal) {
            $this->migrateCategoriesForJournal($journal);
        }
    }

    /**
     * @param TransactionJournal $journal
     */
    private function migrateCategoriesForJournal(TransactionJournal $journal): void
    {
        // grab category from first transaction
        /** @var Transaction $transaction */
        $transaction = $journal->transactions->first();
        if (null === $transaction) {
            // @codeCoverageIgnoreStart
            $this->info(sprintf('Transaction journal #%d has no transactions. Will be fixed later.', $journal->id));

            return;
            // @codeCoverageIgnoreEnd
        }
        /** @var Category $category */
        $category = $transaction->categories->first();
        /** @var Category $journalCategory */
        $journalCategory = $journal->categories->first();

        // both have a category, but they don't match.
        if (null !== $category && null !== $journalCategory && $category->id !== $journalCategory->id) {
            // sync to journal:
            $journal->categories()->sync([(int)$category->id]);
        }

        // transaction has a category, but the journal doesn't.
        if (null !== $category && null === $journalCategory) {
            $journal->categories()->sync([(int)$category->id]);
        }
    }
}

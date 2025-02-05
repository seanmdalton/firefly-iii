<?php
/**
 * PreferenceTransformerTest.php
 * Copyright (c) 2018 thegrumpydictator@gmail.com
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

namespace Tests\Unit\Transformers;


use FireflyIII\Models\Preference;
use FireflyIII\Transformers\PreferenceTransformer;
use Log;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

/**
 * Class PreferenceTransformerTest
 */
class PreferenceTransformerTest extends TestCase
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
     * Test basic tag transformer
     *
     * @covers \FireflyIII\Transformers\PreferenceTransformer
     */
    public function testBasic(): void
    {
        /** @var Preference $preference */
        $preference  = Preference::first();
        $transformer = app(PreferenceTransformer::class);
        $transformer->setParameters(new ParameterBag);

        $result = $transformer->transform($preference);

        $this->assertEquals($preference->name, $result['name']);
    }

}

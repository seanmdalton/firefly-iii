<?php
/**
 * AccessTokenCreatedMail.php
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

namespace FireflyIII\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class AccessTokenCreatedMail
 * @codeCoverageIgnore
 */
class AccessTokenCreatedMail extends Mailable
{

    use Queueable, SerializesModels;

    /** @var string Email address of admin */
    public $email;
    /** @var string IP address of admin */
    public $ipAddress;

    /**
     * AccessTokenCreatedMail constructor.
     *
     * @param string $email
     * @param string $ipAddress
     */
    public function __construct(string $email, string $ipAddress)
    {
        $this->email     = $email;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('emails.access-token-created-html')->text('emails.access-token-created-text')
                    ->subject('A new access token was created');
    }
}

<?php
/**
 * ConfirmEmailChangeMail.php
 * Copyright (c) 2017 thegrumpydictator@gmail.com
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
 * Class ConfirmEmailChangeMail
 *
 * Sends message to new address to confirm change.
 * @codeCoverageIgnore
 */
class ConfirmEmailChangeMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string IP address of user */
    public $ipAddress;
    /** @var string New email address */
    public $newEmail;
    /** @var string Old email address */
    public $oldEmail;
    /** @var string Confirmation link */
    public $uri;

    /**
     * ConfirmEmailChangeMail constructor.
     *
     * @param string $newEmail
     * @param string $oldEmail
     * @param string $uri
     * @param string $ipAddress
     */
    public function __construct(string $newEmail, string $oldEmail, string $uri, string $ipAddress)
    {
        $this->newEmail  = $newEmail;
        $this->oldEmail  = $oldEmail;
        $this->uri       = $uri;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('emails.confirm-email-change-html')->text('emails.confirm-email-change-text')
                    ->subject('Your Firefly III email address has changed');
    }
}

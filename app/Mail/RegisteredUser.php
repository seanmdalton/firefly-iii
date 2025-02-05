<?php

/**
 * RegisteredUser.php
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
 * Sends newly registered user an email message.
 *
 * Class RegisteredUser
 * @codeCoverageIgnore
 */
class RegisteredUser extends Mailable
{
    use Queueable, SerializesModels;
    /** @var string Email address of user */
    public $address;
    /** @var string IP address of user */
    public $ipAddress;

    /**
     * Create a new message instance.
     *
     * @param string $address
     * @param string $ipAddress
     */
    public function __construct(string $address, string $ipAddress)
    {
        $this->address   = $address;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('emails.registered-html')->text('emails.registered-text')->subject('Welcome to Firefly III!');
    }
}

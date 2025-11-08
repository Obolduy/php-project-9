<?php

namespace Hexlet\Code\Tests;

use Hexlet\Code\Config\Messages;
use PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    public function testAllMessagesAreStrings(): void
    {
        $this->assertIsString(Messages::URL_REQUIRED);
        $this->assertIsString(Messages::URL_TOO_LONG);
        $this->assertIsString(Messages::URL_INVALID);
        $this->assertIsString(Messages::URL_ADDED);
        $this->assertIsString(Messages::URL_ALREADY_EXISTS);
        $this->assertIsString(Messages::ERROR_SAVING_URL);
        $this->assertIsString(Messages::ERROR_LOADING_DATA);
        $this->assertIsString(Messages::URL_NOT_FOUND);
        $this->assertIsString(Messages::CHECK_CREATED);
        $this->assertIsString(Messages::ERROR_CREATING_CHECK);
        $this->assertIsString(Messages::CHECK_NETWORK_ERROR);
    }

    public function testAllMessagesAreNotEmpty(): void
    {
        $this->assertNotEmpty(Messages::URL_REQUIRED);
        $this->assertNotEmpty(Messages::URL_TOO_LONG);
        $this->assertNotEmpty(Messages::URL_INVALID);
        $this->assertNotEmpty(Messages::URL_ADDED);
        $this->assertNotEmpty(Messages::URL_ALREADY_EXISTS);
        $this->assertNotEmpty(Messages::ERROR_SAVING_URL);
        $this->assertNotEmpty(Messages::ERROR_LOADING_DATA);
        $this->assertNotEmpty(Messages::URL_NOT_FOUND);
        $this->assertNotEmpty(Messages::CHECK_CREATED);
        $this->assertNotEmpty(Messages::ERROR_CREATING_CHECK);
        $this->assertNotEmpty(Messages::CHECK_NETWORK_ERROR);
    }

    public function testUrlTooLongMessageContainsPlaceholder(): void
    {
        $this->assertStringContainsString('%d', Messages::URL_TOO_LONG);
    }

    public function testUrlTooLongMessageCanBeFormatted(): void
    {
        $formatted = sprintf(Messages::URL_TOO_LONG, 255);

        $this->assertStringContainsString('255', $formatted);
        $this->assertStringNotContainsString('%d', $formatted);
    }

    public function testMessagesAreInRussian(): void
    {
        $this->assertMatchesRegularExpression('/[А-я]/u', Messages::URL_REQUIRED);
        $this->assertMatchesRegularExpression('/[А-я]/u', Messages::URL_INVALID);
        $this->assertMatchesRegularExpression('/[А-я]/u', Messages::URL_ADDED);
    }
}

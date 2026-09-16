<?php

namespace App\Services;

use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class ContactNormalizer
{
    /**
     * Normalize a contact value based on its type.
     */
    public function normalize(string $type, string $value): string
    {
        return match ($type) {
            'email' => $this->normalizeEmail($value),
            'phone', 'whatsapp' => $this->normalizePhone($value),
            default => throw new InvalidArgumentException("Unsupported contact type: {$type}."),
        };
    }

    /**
     * Normalize an email address.
     */
    private function normalizeEmail(string $value): string
    {
        return strtolower(trim($value));
    }

    /**
     * Normalize an international phone or WhatsApp number to E.164 format.
     */
    private function normalizePhone(string $value): string
    {
        $raw = trim($value);
        if ($raw === '') {
            return '';
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // UNKNOWN_REGION parses numbers with country codes (+1, +44, +971, +91, 00...)
            $parsed = $phoneUtil->parse($raw, PhoneNumberUtil::UNKNOWN_REGION);

            if ($phoneUtil->isValidNumber($parsed)) {
                return $phoneUtil->format($parsed, PhoneNumberFormat::E164);
            }
        } catch (NumberParseException $e) {
            // Fall back to digit cleaning if standard parsing fails
        }

        // Clean digits fallback: retain leading '+' if typed
        $digits = preg_replace('/[^\d+]/', '', $raw);

        // Convert standard dial prefix 00 to +
        if (str_starts_with($digits, '00')) {
            $digits = '+' . substr($digits, 2);
        } elseif (!str_starts_with($digits, '+') && strlen($digits) >= 10) {
            $digits = '+' . $digits;
        }

        return $digits;
    }
}

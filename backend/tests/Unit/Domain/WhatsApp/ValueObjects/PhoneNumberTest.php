<?php

use App\Domain\WhatsApp\ValueObjects\PhoneNumber;

describe('PhoneNumber Creation', function () {
    it('can create from valid Colombian string', function () {
        $phoneString = '+573218278325';
        $phoneNumber = PhoneNumber::fromString($phoneString);

        expect($phoneNumber->getValue())->toBe($phoneString);
    });

    it('normalizes Colombian mobile number', function () {
        $phoneNumber = PhoneNumber::fromString('3218278325');

        expect($phoneNumber->getValue())->toBe('+573218278325');
    });

    it('normalizes Colombian number without plus', function () {
        $phoneNumber = PhoneNumber::fromString('573218278325');

        expect($phoneNumber->getValue())->toBe('+573218278325');
    });
});

describe('PhoneNumber Validation', function () {
    it('rejects empty string', function () {
        expect(fn() => PhoneNumber::fromString(''))
            ->toThrow(InvalidArgumentException::class, 'Phone number cannot be empty');
    });

    it('rejects invalid format', function () {
        expect(fn() => PhoneNumber::fromString('invalid-phone'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('rejects non-Colombian numbers', function () {
        expect(fn() => PhoneNumber::fromString('+1234567890'))
            ->toThrow(InvalidArgumentException::class, 'Invalid phone number format. Expected Colombian mobile number.');
    });
});

describe('PhoneNumber Equality', function () {
    it('compares correctly', function () {
        $phone1 = PhoneNumber::fromString('+573218278325');
        $phone2 = PhoneNumber::fromString('3218278325');
        $phone3 = PhoneNumber::fromString('+573001234567');

        expect($phone1->equals($phone2))->toBeTrue();
        expect($phone1->equals($phone3))->toBeFalse();
    });
});

describe('PhoneNumber Formatting', function () {
    it('formats for display', function () {
        $phoneNumber = PhoneNumber::fromString('+573218278325');

        expect($phoneNumber->getDisplayFormat())->toBe('+57 321 827 8325');
    });

    it('adds whatsapp prefix', function () {
        $phoneNumber = PhoneNumber::fromString('+573218278325');

        expect($phoneNumber->getWhatsAppFormat())->toBe('whatsapp:+573218278325');
    });

    it('converts to string', function () {
        $phoneString = '+573218278325';
        $phoneNumber = PhoneNumber::fromString($phoneString);

        expect((string) $phoneNumber)->toBe($phoneString);
    });
});
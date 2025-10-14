<?php

use App\Infrastructure\WhatsApp\Services\FuzzyKeywordMatcher;

describe('FuzzyKeywordMatcher', function () {
    beforeEach(function () {
        $this->matcher = new FuzzyKeywordMatcher();
    });

    describe('findFuzzyMatches', function () {
        it('finds exact matches', function () {
            $keywords = ['hello', 'world', 'test'];
            $matches = $this->matcher->findFuzzyMatches('hello', $keywords);

            expect($matches)->toContain('hello');
        });

        it('finds fuzzy matches within distance', function () {
            $keywords = ['hello', 'world', 'test'];
            $matches = $this->matcher->findFuzzyMatches('helo', $keywords, 2);

            expect($matches)->toContain('hello');
        });

        it('finds substring matches', function () {
            $keywords = ['greeting', 'hello world', 'test'];
            $matches = $this->matcher->findFuzzyMatches('hello', $keywords);

            expect($matches)->toContain('hello world');
        });

        it('excludes matches beyond threshold', function () {
            $keywords = ['completely', 'different', 'words'];
            $matches = $this->matcher->findFuzzyMatches('hello', $keywords, 2);

            expect($matches)->toBeEmpty();
        });

        it('is case insensitive', function () {
            $keywords = ['HELLO', 'World', 'TeSt'];
            $matches = $this->matcher->findFuzzyMatches('hello', $keywords);

            expect($matches)->toContain('HELLO');
        });
    });

    describe('calculateDistance', function () {
        it('calculates correct Levenshtein distance', function () {
            $distance = $this->matcher->calculateDistance('hello', 'helo');
            expect($distance)->toBe(1);

            $distance = $this->matcher->calculateDistance('test', 'best');
            expect($distance)->toBe(1);

            $distance = $this->matcher->calculateDistance('same', 'same');
            expect($distance)->toBe(0);
        });
    });

    describe('areSimilar', function () {
        it('returns true for exact matches', function () {
            expect($this->matcher->areSimilar('hello', 'hello'))->toBeTrue();
        });

        it('returns true for substring matches', function () {
            expect($this->matcher->areSimilar('hello', 'hello world'))->toBeTrue();
            expect($this->matcher->areSimilar('hello world', 'hello'))->toBeTrue();
        });

        it('returns true for similar strings within threshold', function () {
            expect($this->matcher->areSimilar('hello', 'helo', 2))->toBeTrue();
        });

        it('returns false for different strings beyond threshold', function () {
            expect($this->matcher->areSimilar('hello', 'world', 2))->toBeFalse();
        });
    });
});
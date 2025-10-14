<?php

use App\Application\WhatsApp\DTOs\SendMessageDTO;
use App\Application\WhatsApp\UseCases\SendWhatsAppMessageUseCase;
use App\Domain\WhatsApp\Services\WhatsAppServiceInterface;
use App\Domain\WhatsApp\Services\WhatsAppResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Send WhatsApp Message Feature', function () {
    beforeEach(function () {
        $this->mockWhatsAppService = Mockery::mock(WhatsAppServiceInterface::class);
        $this->app->instance(WhatsAppServiceInterface::class, $this->mockWhatsAppService);
    });

    describe('API Endpoint', function () {
        it('can send message via POST request', function () {
            $this->mockWhatsAppService
                ->shouldReceive('sendMessage')
                ->once()
                ->andReturn(WhatsAppResponse::success('MSG123', 'Message sent successfully'));

            $response = $this->postJson('/api/whatsapp/send', [
                'phone_number' => '+573218278325',
                'content' => 'Hello from Pest test!'
            ]);

            $response->assertStatus(201)
                ->assertJson([
                    'success' => true
                ])
                ->assertJsonStructure([
                    'message_id'
                ]);
        });

        it('validates required fields', function () {
            $response = $this->postJson('/api/whatsapp/send', []);

            $response->assertStatus(400)
                ->assertJson([
                    'success' => false
                ]);
        });

        it('validates phone number format', function () {
            $response = $this->postJson('/api/whatsapp/send', [
                'phone_number' => '+1234567890',
                'content' => 'Test message'
            ]);

            $response->assertStatus(400)
                ->assertJson([
                    'success' => false
                ]);
        });
    });

    describe('Use Case', function () {
        it('executes successfully', function () {
            $this->mockWhatsAppService
                ->shouldReceive('sendMessage')
                ->once()
                ->andReturn(WhatsAppResponse::success('MSG123', 'Success'));

            $useCase = $this->app->make(SendWhatsAppMessageUseCase::class);
            $dto = new SendMessageDTO('+573218278325', 'Test message');

            $result = $useCase->execute($dto);

            expect($result->isSuccess())->toBeTrue();
            expect($result->getMessageId())->not()->toBeEmpty();
        });

        it('handles phone validation errors', function () {
            $useCase = $this->app->make(SendWhatsAppMessageUseCase::class);
            $dto = new SendMessageDTO('+1234567890', 'Test message');

            $result = $useCase->execute($dto);

            expect($result->isSuccess())->toBeFalse();
            expect($result->getErrorMessage())->toContain('Invalid phone number format');
        });
    });
});
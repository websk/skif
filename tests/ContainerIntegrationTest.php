<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use WebSK\Skif\Poll\PollQuestionService;
use WebSK\Skif\Poll\PollService;
use WebSK\Skif\Poll\RequestHandlers\PollViewHandler;

final class ContainerIntegrationTest extends TestCase
{
    public function testPhpDiResolvesHandlerWithInjectAttributes(): void
    {
        $poll_service = $this->createStub(PollService::class);
        $poll_question_service = $this->createStub(PollQuestionService::class);

        $builder = new ContainerBuilder();
        $builder->useAttributes(true);
        $builder->addDefinitions([
            PollService::class => $poll_service,
            PollQuestionService::class => $poll_question_service,
        ]);

        $handler = $builder->build()->get(PollViewHandler::class);

        self::assertInstanceOf(PollViewHandler::class, $handler);
        self::assertSame(
            $poll_service,
            (new \ReflectionProperty($handler, 'poll_service'))->getValue($handler)
        );
        self::assertSame(
            $poll_question_service,
            (new \ReflectionProperty($handler, 'poll_question_service'))->getValue($handler)
        );
    }
}

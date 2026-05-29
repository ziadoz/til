<?php

declare(strict_types=1);

use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticRegistry;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class StubDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'stub.test';
    }

    public function label(): string
    {
        return 'Stub Test';
    }

    public function run(array $options = []): DiagnosticResult
    {
        return DiagnosticResult::ok('OK');
    }
}

final class AnotherStubDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'stub.other';
    }

    public function label(): string
    {
        return 'Another Stub';
    }

    public function run(array $options = []): DiagnosticResult
    {
        return DiagnosticResult::ok('OK');
    }
}

final class DifferentGroupDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'other.thing';
    }

    public function label(): string
    {
        return 'Different Group';
    }

    public function run(array $options = []): DiagnosticResult
    {
        return DiagnosticResult::ok('OK');
    }
}

it('registers a diagnostic instance', function (): void {
    $registry = new DiagnosticRegistry;
    $registry->register(new StubDiagnostic);

    expect($registry->has('stub.test'))->toBeTrue();
});

it('registers a diagnostic by class name', function (): void {
    $registry = new DiagnosticRegistry;
    $registry->register(StubDiagnostic::class);

    expect($registry->has('stub.test'))->toBeTrue();
});

it('throws when registering an invalid class', function (): void {
    $registry = new DiagnosticRegistry;
    $registry->register(stdClass::class);
})->throws(InvalidArgumentException::class);

it('finds a diagnostic by name', function (): void {
    $registry = new DiagnosticRegistry;
    $registry->register(StubDiagnostic::class);

    expect($registry->find('stub.test'))->toBeInstanceOf(StubDiagnostic::class);
    expect($registry->find('does.not.exist'))->toBeNull();
});

it('finds many diagnostics by name', function (): void {
    $registry = new DiagnosticRegistry;
    $registry->register(StubDiagnostic::class);
    $registry->register(AnotherStubDiagnostic::class);

    $found = $registry->findMany(['stub.test', 'stub.other', 'does.not.exist']);

    expect($found)->toHaveCount(2);
    expect($found->has('stub.test'))->toBeTrue();
    expect($found->has('stub.other'))->toBeTrue();
});

it('lists all diagnostics sorted by name', function (): void {
    $registry = new DiagnosticRegistry;
    $registry->register(AnotherStubDiagnostic::class);
    $registry->register(StubDiagnostic::class);

    expect($registry->all()->keys()->all())->toBe(['stub.other', 'stub.test']);
});

it('returns diagnostics in a group', function (): void {
    $registry = new DiagnosticRegistry;
    $registry->register(StubDiagnostic::class);
    $registry->register(AnotherStubDiagnostic::class);
    $registry->register(DifferentGroupDiagnostic::class);

    $group = $registry->group('stub');

    expect($group)->toHaveCount(2);
    expect($group->has('stub.test'))->toBeTrue();
    expect($group->has('stub.other'))->toBeTrue();
    expect($group->has('other.thing'))->toBeFalse();
});

it('forgets a diagnostic', function (): void {
    $registry = new DiagnosticRegistry;
    $registry->register(StubDiagnostic::class);
    $registry->forget('stub.test');

    expect($registry->has('stub.test'))->toBeFalse();
});

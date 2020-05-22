<?php
// Closure can't be mocked because it's a final class.
// Mock another class instead and add __invoke to it.

$mockClosure = $this->getMockBuilder(\stdClass::class)
    ->addMethods(['__invoke'])
    ->getMock();

$mockClosure->expects($this->exactly(1))
    ->method('__invoke')
    ->willReturn('Hello, World!');
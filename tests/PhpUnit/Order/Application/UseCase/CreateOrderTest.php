<?php

namespace App\Tests\PhpUnit\Order\Application\UseCase;

use App\Order\Adapter\Doctrine\OrderRepository;
use App\Order\Application\UseCase\CreateOrder;
use App\Order\Domain\Model\Order;
use App\User\Adapter\Doctrine\UserRepository;
use App\User\Domain\Model\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateOrderTest extends TestCase
{
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;
    private CreateOrder $case;

    public function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->case = new CreateOrder($this->orderRepository, $this->userRepository);
    }

    public function testExecute(): void
    {
        $user = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $this->orderRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function (Order $order) use ($user) {
                $reflection = new \ReflectionClass($order);
                $property = $reflection->getProperty('id');
                $property->setValue($order, 123);
            });
        $this->assertEquals(123, $this->case->execute(1));
    }

    public function testExecuteUserNotFound(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('User not found');

        $this->case->execute(1);
    }
}

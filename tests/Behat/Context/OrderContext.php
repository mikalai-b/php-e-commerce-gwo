<?php

namespace App\Tests\Behat\Context;

use App\Tests\Behat\Model\RequestModel;
use Behat\Behat\Context\Context;
use Doctrine\ORM\Tools\ToolsException;
use Exception;
use PHPUnit\Framework\Assert as Assertions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class OrderContext implements Context
{
    private array $storage = [];
    private RequestModel $request;
    private Response $response;


    /**
     * @throws ToolsException
     */
    public function __construct(
        private readonly KernelInterface $kernel,
    ) {
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(): void
    {
        $this->request = new RequestModel();
    }

    /**
     * Sets an HTTP Header.
     *
     * @param string $name  header name
     * @param string $value header value
     *
     * @Given I set header :name with value :value
     */
    public function iSetHeaderWithValue(string $name, string $value)
    {
        $this->request->setHeader($name, $value);
    }

    /**
     * Sends HTTP request to specific relative URL.
     *
     * @param string $method request method
     * @param string $url relative url
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)"$/
     * @throws Exception
     */
    public function iSendARequest(string $method, string $url)
    {
        $this->request->setMethod($method);
        $this->request->setUrl($this->replaceVariables($url));

        ob_start();
        $this->response = $this->kernel->handle($this->request->createRequest());
        ob_end_clean();
    }

    /**
     * Checks that response has specific status code.
     *
     * @Then /^(?:the )?response code should be (\d+)$/
     */
    public function theResponseCodeShouldBe($code)
    {
        if (empty($this->response)) {
            throw new \RuntimeException('No response received');
        }

        $expected = (int) $code;
        $actual = $this->response->getStatusCode();
        Assertions::assertSame($expected, $actual);
    }


    /**
     * @Then the response should have :property = :value
     */
    public function assertResponsePropertyValueEquals(string $property, mixed $value): void
    {
        if (empty($this->response)) {
            throw new \RuntimeException('No response received');
        }

        $response = json_decode($this->response->getContent() ?: '', true, JSON_THROW_ON_ERROR);
        Assertions::assertEquals(
            $value,
            $response[$property],
            "{$response[$property]} does not equal to expected {$value}"
        );
    }

    /**
     * @Then i store response data :dataPath as :variableName
     *
     * @param string $dataPath
     * @param string $variableName
     *
     * @throws Exception
     */
    public function iStoreResponseDataAs(string $dataPath, string $variableName)
    {
        $response = json_decode($this->response->getContent() ?: '', true, JSON_THROW_ON_ERROR);
        $property = $response[$dataPath]; //need add path parser

        $this->storage[$variableName] = $property;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function replaceVariables($string)
    {
        $pattern = '/%[^&]+.%/iU';

        preg_match_all($pattern, $string, $matches);

        if (count($matches) == 1) {
            foreach ($matches[0] as $match) {
                $variable = str_replace('%', '', $match);
                $string = str_replace($match, $this->storage[$variable], $string);
            }
        }

        return $string;
    }
}
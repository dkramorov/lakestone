<?php

namespace Lakestone\SubCommon\Service\AWS;

use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Lakestone\Config;
use Lakestone\SubCommon\Structures\Aws\Queue\AwsQueueMessageStructureInterface;
use Lakestone\Trait\LoggedSingleton;

class SQS {
  
  use LoggedSingleton;
  
  protected ?SqsClient $client = null;
  protected $repeateLimit = 5;
  
  protected function getClient(): SqsClient {
    
    if ($this->client == null) {
      $this->client = new SqsClient([
          'version' => 'latest',
          'region' => Config::getInstance()->getParam('aws.SQS.region'),
          'credentials' => new Credentials(
              Config::getInstance()->getParam('aws.SQS.key'),
              Config::getInstance()->getParam('aws.SQS.secret')
          ),
      ]);
    }
    
    return $this->client;
    
  }
  
  public function sendMessage(AwsQueueMessageStructureInterface $message): array {
    $result = [];
    $counter = 1;
    do {
      try {
        $this->logger->debug('message to send', ['message' => $message->toArray()]);
        $result = $this->getClient()->sendMessage($message->toArray());
        $this->logger->debug('message sent', ['result' => $result->toArray()]);
        $result = $result->toArray();
        break;
      } catch (AwsException $exception) {
        $this->logger->error('Unable to push (attempt ' . $counter . ') the message: ' . $exception->getMessage(), ['message' => $message->toArray()]);
      }
    } while ($counter++ < $this->repeateLimit);
    return $result;
  }
  
  public function pullMessage(string $url): ?array {
    $result = [];
    $counter = 1;
    $isDone = false;
    do {
      try {
        $result = $this->getClient()->receiveMessage(array(
            'AttributeNames' => ['All'],
            'MaxNumberOfMessages' => 1,
            'MessageAttributeNames' => ['All'],
            'QueueUrl' => $url,
            'WaitTimeSeconds' => 0,
        ));
        $this->logger->debug('received messages from: ' . $url, ['result' => $result->get('Messages')]);
        $result = $result->get('Messages');
        $isDone = true;
      } catch (AwsException $exception) {
        $this->logger->error('Unable to pull (attempt ' . $counter . ') the message: ' . $exception->getMessage());
      }
    } while (
        ! $isDone
        and $counter++ < $this->repeateLimit
    );
    if (!$isDone) {
      $this->logger->emergency('Could not pull the message');
    }
    return $result;
  }
  
  public function deleteMessage(string $url, string $receiptHandle): void {
    $counter = 1;
    $isDone = false;
    $params = [
        'QueueUrl' => $url,
        'ReceiptHandle' => $receiptHandle,
    ];
    do {
      try {
        $this->logger->debug('delete messages from: ' . $url, ['ReceiptHandle' => $receiptHandle, 'counter' => $counter]);
        $this->getClient()->deleteMessage($params);
        $isDone = true;
      } catch (AwsException $exception) {
        $this->logger->error('Unable to delete (attempt ' . $counter . ') the message: ' . $exception->getMessage(), $params);
      }
    } while (
        ! $isDone
        and $counter++ < $this->repeateLimit
    );
    if (!$isDone) {
      $this->logger->emergency('Could not delete the message', $params);
    }
  }
  
}
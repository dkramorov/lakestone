<?php

namespace Lakestone\SubCommon\Service\AWS;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;
use Aws\Credentials\Credentials;
use http\Exception\RuntimeException;
use JetBrains\PhpStorm\ArrayShape;
use Lakestone\Config;
use Lakestone\Trait\LoggedSingleton;

class CloudWatchLogs {
  
  use LoggedSingleton;
  
  protected float $lastPutEvent;
  protected array $batchPutEvent;
  protected ?CloudWatchLogsClient $client = null;
  protected string $groupName;
  protected string $streamName;
  /**
   * @var array<string, array{nextSequenceToken: string}>
   */
  protected array $streams = [];
  protected ?string $nextSequenceToken = null;
  
  // todo: need to add PutRetentionPolicy
  // https://docs.aws.amazon.com/AmazonCloudWatchLogs/latest/APIReference/API_PutRetentionPolicy.html
  
  public function getClient(): CloudWatchLogsClient {
    
    if ($this->client == null) {
      $this->client = new CloudWatchLogsClient([
          'version' => 'latest',
          'region' => Config::getInstance()->getParam('aws.CloudWatch.region'),
          'credentials' => new Credentials(
              Config::getInstance()->getParam('aws.CloudWatch.key'),
              Config::getInstance()->getParam('aws.CloudWatch.secret')
          ),
      ]);
    }
    
    return $this->client;
    
  }
  
  /**
   * Puts $textEvent to $groupName:$streamName and returns $nextSequenceToken
   * Uses exists $nextSequenceToken if it not null
   * @param mixed $dataEvent
   * @return string
   */
  public function putLogEvent(mixed $dataEvent, bool $async = false): ?string {
    $return = null;
    $limit = 5;
    $request = [
        'logEvents' => [
            [
                'message' => json_encode($dataEvent),
                'timestamp' => (int)microtime(true) * 1000,
            ],
        ],
        'logGroupName' => $this->groupName,
        'logStreamName' => $this->streamName,
    ];
    if (!empty($this->streams[$this->streamName]['nextSequenceToken'])) {
      $request['sequenceToken'] = $this->streams[$this->streamName]['nextSequenceToken'];
    }
    do {
      try {
        if ($async) {
          throw new RuntimeException('Not supported now');
          $promise = $this->getClient()->putLogEventsAsync($request);
          $this->lastPutEvent = microtime(true);
          $this->logger->debug('got1', ['debug' => $promise->getState()]);
          $promise->wait();
          $this->logger->debug('got2', ['debug' => $promise->getState()]);
          $promise->then(
              function ($result) {
                $this->logger->debug('done', ['debug' => $result->get('nextSequenceToken')]);
                $this->streams[$this->streamName]['nextSequenceToken'] = $result->get('nextSequenceToken');
              },
              fn($result) => $this->logger->debug('fail')
          );
        } else {
          $result = $this->getClient()->putLogEvents($request);
          $this->lastPutEvent = microtime(true);
          $return = $this->streams[$this->streamName]['nextSequenceToken'] = $result->get('nextSequenceToken');
        }
        break;
      } catch (CloudWatchLogsException $e) {
        switch (true) {
          case $e->getAwsErrorCode() == 'ResourceNotFoundException':
            $this->logger->error('The ResourceNotFoundException in ' . __METHOD__);
            $this->logger->debug('The ResourceNotFoundException in ' . __METHOD__, ['request' => $request, 'exception' => $e]);
            $this->createStream($this->streamName);
            break;
          case $e->getAwsErrorCode() == 'ThrottlingException':
            $this->logger->debug('The ThrottlingException in ' . __METHOD__ . ': ' . $this->streamName, [
                'timeout' => microtime(true) - $this->lastPutEvent,
                'streamName' => $this->streamName,
                'request' => $request,
                'exception' => $e
            ]);
            usleep(300000); // 200000
            break;
          default:
            $this->logger->error('CloudWatchLogsException in ' . __METHOD__ . ': ' . $e->getMessage());
            $this->logger->debug('CloudWatchLogsException in ' . __METHOD__ . ': ' . $e->getMessage(), ['exception' => $e]);
            break 2;
        }
      } catch (\Throwable $e) {
        $this->logger->error('Error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'dataEvent' => $dataEvent,
                'logGroupName' => $this->groupName,
                'logStreamName' => $this->streamName,
            ]
        );
        $this->logger->debug('Error in ' . __METHOD__ . ': ' . $e->getMessage(), [
                'dataEvent' => $dataEvent,
                'logGroupName' => $this->groupName,
                'logStreamName' => $this->streamName,
                'exception' => $e,
                'trace' => $e->getTrace()
            ]
        );
        break;
      }
    } while ($limit-- > 0);
    if ($limit <= 0) {
      $this->logger->debug('Could not complete the request putLogEvents in ' . __METHOD__, ['request' => $request]);
    }
    return $return;
  }
  
  /**
   * @return string
   */
  public function getGroupName(): string {
    return $this->groupName;
  }
  
  /**
   * @param string $groupName
   * @return CloudWatchLogs
   */
  public function setGroupName(string $groupName): CloudWatchLogs {
    /*    try {
          $this->getClient()->createLogGroupAsync([
              'logGroupName' => $this->groupName,
          ]);
        } catch (\Throwable $e) {
          $this->logger->debug('Error in setGroupName: ' . $e->getMessage(), ['exception' => $e]);
        }*/
    $this->groupName = $groupName;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getStreamName(): string {
    return $this->streamName;
  }
  
  /**
   * Creates new $streamName and resets $nextSequenceToken
   * @param string $streamName
   * @return CloudWatchLogs
   */
  public function setStreamName(string $streamName, bool $create = true): CloudWatchLogs {
    $this->streamName = $streamName;
    return $this;
  }
  
  public function setupStream(string $streamName = null): self {
    if ($streamName !== null) {
      $this->streamName = $streamName;
    }
    if (empty($this->streams[$this->streamName])) {
      if ($this->getStreams($streamName) == 0) {
        $this->createStream($this->streamName);
      }
    }
    return $this;
  }
  
  public function getStreams(string $streamNamePrefix = null): int {
    $limit = 5;
    $streams = [];
    $request = [
        'logGroupName' => $this->groupName,
    ];
    if ($streamNamePrefix !== null) {
      $request['logStreamNamePrefix'] = $streamNamePrefix;
    }
    do {
      try {
        $result = $this->getClient()
            ->describeLogStreams($request);
        $streams = $result->get('logStreams') ?? [];
        foreach ($streams as $stream) {
          $this->streams[$stream['logStreamName']]['nextSequenceToken'] = $stream['uploadSequenceToken'] ?? null;
        }
        break;
      } catch (CloudWatchLogsException $e) {
        $this->logger->error('CloudWatchLogsException (' . $e->getAwsErrorCode() . ') in ' . __METHOD__ . ': ' . $e->getMessage());
        $this->logger->debug('CloudWatchLogsException (' . $e->getAwsErrorCode() . ') in ' . __METHOD__ . ': ' . $e->getMessage(), ['request' => $request, 'exception' => $e]);
        switch (true) {
          case $e->getAwsErrorCode() == 'ThrottlingException':
            usleep(300000); // 200000
            $this->logger->debug('The pause is completed');
            break;
        }
      } catch (\Throwable $e) {
        $this->logger->error('Error in ' . __METHOD__ . ': ' . $e->getMessage());
        $this->logger->debug('Error in ' . __METHOD__ . ': ' . $e->getMessage(), ['request' => $request, 'exception' => $e]);
        break;
      }
      
    } while ($limit-- > 0);
    if ($limit <= 0) {
      $this->logger->debug('Could not complete the request describeLogStreams in ' . __METHOD__, ['request' => $request]);
    }
    return sizeof($streams);
  }
  
  public function createStream(string $streamName): self {
    try {
      $this->getClient()->createLogStream([
          'logGroupName' => $this->groupName,
          'logStreamName' => $streamName,
      ]);
      $this->streams[$streamName] = [
          'nextSequenceToken' => null,
      ];
      $this->streamName = $streamName;
    } catch (\Throwable $e) {
      $this->logger->error('Error in ' . __METHOD__ . ': ' . $e->getMessage(), ['exception' => $e]);
    }
    return $this;
    
  }
  
  /**
   * @return string
   */
  public function getNextSequenceToken(): string {
    return $this->nextSequenceToken;
  }
  
  /**
   * @param string $nextSequenceToken
   * @return CloudWatchLogs
   */
  public function setNextSequenceToken(?string $nextSequenceToken): CloudWatchLogs {
    $this->nextSequenceToken = $nextSequenceToken;
    return $this;
  }
  
  
}
<?php

namespace Lakestone\SubCommon\Interface;

interface AwsServiceInterface {

  /**
   * SQS
   */
//  const queueOrderUrl = 'https://sqs.eu-north-1.amazonaws.com/278719026608/Orders.fifo';
  const messageOrderGroup = 'orders';
  const queueOrderUrl = 'https://sqs.eu-north-1.amazonaws.com/278719026608/Orders';
  
  /**
   * CloudWatchLogs
   */
  const productSyncGroup = 'product-sync';
  
}
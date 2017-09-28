<?php

namespace BehatReportPortal;

use Psr\Http\Message\ResponseInterface;
use ReportPortalBasic\Enum\ItemStatusesEnum;
use ReportPortalBasic\Enum\ItemTypesEnum;
use ReportPortalBasic\Service\ReportPortalHTTPService;

/**
 * Report portal HTTP/BDD service.
 * Provides basic methods to collaborate with Report portal with BDD framework.
 */
class ReportPortalHTTP_BDDService extends ReportPortalHTTPService
{

    /**
     * Create feature item
     *
     * @param string $name
     *            - feature name
     * @return ResponseInterface - result of request
     */
    public static function createFeatureItem(string $name)
    {
        $result = self::startChildItem(self::$rootItemID, self::DEFAULT_FEATURE_DESCRIPTION, $name, ItemTypesEnum::SUITE, array());
        self::$featureItemID = self::getValueFromResponse('id', $result);
        return $result;
    }

    /**
     * Create scenario item
     *
     * @param string $name
     *            - scenario name
     * @param string $description
     *            - sceanrio description
     * @return ResponseInterface - result of request
     */
    public static function createScenarioItem(string $name, string $description)
    {
        $result = self::startChildItem(self::$featureItemID, $description, $name, ItemTypesEnum::TEST, array());
        self::$scenarioItemID = self::getValueFromResponse('id', $result);
        return $result;
    }

    /**
     * Create step item
     *
     * @param string $name
     *            - step name
     * @return ResponseInterface - result of request
     */
    public static function createStepItem(string $name)
    {
        $result = self::startChildItem(self::$scenarioItemID, self::DEFAULT_STEP_DESCRIPTION, $name, ItemTypesEnum::STEP, array());
        self::$stepItemID = self::getValueFromResponse('id', $result);
        return $result;
    }

    /**
     * Finish step item
     *
     * @param string $itemStatus
     *            - step item status
     * @param string $description
     *            - step description
     * @param string $stackTrace
     *            - stack trace
     * @return ResponseInterface - result of request
     */
    public static function finishStepItem(string $itemStatus, string $description, string $stackTrace)
    {
        $actualDescription = '';
        if ($itemStatus == ItemStatusesEnum::SKIPPED) {
            self::addLogMessage(self::$stepItemID, $description, 'info');
            $actualDescription = $description;
        }
        if ($itemStatus == ItemStatusesEnum::FAILED) {
            self::addLogMessage(self::$stepItemID, $stackTrace, 'error');
            $actualDescription = $description;
        }
        $result = self::finishItem(self::$stepItemID, $itemStatus, $actualDescription);
        self::$stepItemID = self::EMPTY_ID;
        return $result;
    }

    /**
     * Finish scenario item
     *
     * @param string $scenarioStatus
     *            - scenario status
     * @return ResponseInterface - result of request
     */
    public static function finishScenarioItem(string $scenarioStatus)
    {
        $result = self::finishItem(self::$scenarioItemID, $scenarioStatus, '');
        self::$scenarioItemID = self::EMPTY_ID;
        return $result;
    }

    /**
     * Finish feature item
     *
     * @param string $testStatus
     *            - feature status
     * @param string $description
     *            - feature item description
     * @return ResponseInterface - result of request
     */
    public static function finishFeatureItem(string $testStatus, string $description)
    {
        $result = self::finishItem(self::$featureItemID, $testStatus, $description);
        self::$featureItemID = self::EMPTY_ID;
        return $result;
    }
    
    /**
     * Add log message with picture.
     * @param string $message
     * @param string $logLevel of log
     * @param string $pictureAsString - picture content
     */
    public static function addLogMessagePicture(string $message, string $logLevel, string $pictureAsString)
    {
        ReportPortalHTTPService::addLogMessageWithPicture($pictureAsString,self::$stepItemID,$message,$logLevel,'png');
    }
}

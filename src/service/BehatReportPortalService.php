<?php
namespace BehatReportPortal;

use Behat\Behat\Hook\Scope\AfterFeatureScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Hook\Scope\HookScope;
use Behat\Testwork\Tester\Result\TestResults;
use ReportPortalBasic\Enum\ItemStatusesEnum;
use ReportPortalBasic\Service\ReportPortalHTTPService;
use TestFramework\Services\AssertService;

/**
 * Service to build collaborations between Behat and Report portal.
 *
 * @author Mikalai_Kabzar
 *        
 */
class BehatReportPortalService
{

    private const SCENARIO_OUTLINE_KEYWORD = 'Example';

    private static $arrayWithSteps = array();

    private static $arrayWithScenarios = array();

    private static $arrayWithFeatures = array();

    private static $launchPrefix = 'Test run - ';

    /**
     *
     * @var ReportPortalHTTP_BDDService
     */
    protected static $httpService;

    public $result = 0;

    /**
     * Set launch prefix
     * 
     * @param string $launchPrefix
     *            - launch prefix to set
     */
    public static function setLaunchPrefix(string $launchPrefix)
    {
        self::$launchPrefix = $launchPrefix;
    }

    /**
     * Start launch
     *
     * @param BeforeSuiteScope $event
     *            - before suite event
     */
    public static function startLaunch(BeforeSuiteScope $event)
    {
        $suiteName = $event->getSuite()->getName();
        self::$httpService = new ReportPortalHTTP_BDDService();
        self::$httpService->launchTestRun(self::$launchPrefix . $suiteName, '', ReportPortalHTTPService::DAFAULT_LAUNCH_MODE, array());
        self::$httpService->createRootItem($suiteName, '', array());
    }

    /**
     * Start feature
     *
     * @param BeforeFeatureScope $event
     *            - before feature event
     */
    public static function startFeature(BeforeFeatureScope $event)
    {
        $featureName = $event->getFeature()->getTitle();
        $keyWord = $event->getFeature()->getKeyword();
        self::$httpService->createFeatureItem($keyWord . ' : ' . $featureName, '');
    }

    /**
     * Start scenario
     *
     * @param BeforeScenarioScope $event
     *            - before scenario event
     */
    public static function startScenario(BeforeScenarioScope $event)
    {
        self::$arrayWithSteps = array();
        $keyWord = $event->getScenario()->getKeyword();
        $scenarioTitle = $event->getScenario()->getTitle();
        $description = '';
        if (self::SCENARIO_OUTLINE_KEYWORD == $keyWord) {
            $scenarios = $event->getFeature()->getScenarios();
            $scenarioLine = $event->getScenario()->getLine();
            $scenarioIndex = 0;
            for ($i = 0; $i < sizeof($scenarios); $i ++) {
                if ($scenarioLine >= $scenarios[$i]->getLine()) {
                    $scenarioIndex = $i;
                }
            }
            $scenario = $event->getFeature()->getScenarios()[$scenarioIndex];
            $scenarioName = $scenario->getKeyword() . ' : ' . $scenario->getTitle();
            $description = $keyWord . ' : ' . $scenarioTitle;
        } else {
            $scenarioName = $keyWord . ' : ' . $scenarioTitle;
            $description = '';
        }
        self::$httpService->createScenarioItem($scenarioName, $description);
    }

    /**
     * Start step
     *
     * @param BeforeStepScope $event
     *            - before step event
     */
    public static function startStep(BeforeStepScope $event)
    {
        $keyWord = $event->getStep()->getKeyword();
        $stepName = $event->getStep()->getText();
        self::$httpService->createStepItem($keyWord . ' : ' . $stepName);
    }

    /**
     * Finish step
     *
     * @param AfterStepScope $event
     *            - after step event
     */
    public static function finishStep(AfterStepScope $event)
    {
        array_push(self::$arrayWithSteps, $event->getStep());
        $status = self::getEventStatus($event);
        self::$httpService->finishStepItem($status, AssertService::getAssertMessage(), AssertService::getStackTraceMessage());
    }

    /**
     * Finish scenario
     *
     * @param AfterScenarioScope $event
     *            - after scenario event
     */
    public static function finishScenario(AfterScenarioScope $event)
    {
        $fullArrayWithStep = $event->getScenario()->getSteps();
        $diffArray = array_udiff($fullArrayWithStep, self::$arrayWithSteps, function ($obj_a, $obj_b) {
            return strcmp($obj_a->getText(), $obj_b->getText());
        });
        $lastFailedStep = '';
        if (count($diffArray) > 0) {
            $lastFailedStep = end(self::$arrayWithSteps)->getText();
        }
        foreach ($diffArray as $value) {
            $keyWord = $value->getKeyword();
            $stepName = $value->getText();
            self::$httpService->createStepItem($keyWord . ' : ' . $stepName);
            self::$httpService->finishStepItem(ItemStatusesEnum::SKIPPED, 'SKIPPED. Skipped due to failure of \'' . $lastFailedStep . '\'.', AssertService::getStackTraceMessage());
        }
        $status = self::getEventStatus($event);
        self::$httpService->finishScrenarioItem($status);
    }

    /**
     * Finish test feature
     *
     * @param AfterFeatureScope $event
     *            - after feature event
     */
    public static function finishFeature(AfterFeatureScope $event)
    {
        $featureDescription = $event->getFeature()->getDescription();
        $status = self::getEventStatus($event);
        self::$httpService->finishFeatureItem($status, $featureDescription);
    }

    /**
     * Finish test launch
     *
     * @param AfterSuiteScope $event
     *            - after suite event
     */
    public static function finishLaunch(AfterSuiteScope $event)
    {
        $status = self::getEventStatus($event);
        self::$httpService->finishRootItem($status);
        self::$httpService->finishTestRun($status);
    }

    /**
     * Get Behat event status in Report portal format
     *
     * @param HookScope $event
     *            -
     *            Behat event
     * @return string with status in Report portal format
     */
    private static function getEventStatus(HookScope $event)
    {
        $statusCode = $event->getTestResult()->getResultCode();
        switch ($statusCode) {
            case TestResults::PASSED:
                $status = ItemStatusesEnum::PASSED;
                break;
            case TestResults::FAILED:
                $status = ItemStatusesEnum::FAILED;
                break;
            case TestResults::SKIPPED:
                $status = ItemStatusesEnum::SKIPPED;
                break;
        }
        return $status;
    }
}
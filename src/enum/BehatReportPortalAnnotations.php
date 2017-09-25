<?php
namespace ReportPortal\Behat;

use Behat\Testwork\Hook\Scope\HookScope;

/**
 * Basic report portal annotations for Behat
 * 
 * @author Mikalai_Kabzar
 *
 */
interface BehatReportPortalAnnotations
{

    /**
     * @BeforeSuite
     */
    public static function startLaunch(HookScope $event);

    /**
     * @BeforeFeature
     */
    public static function startFeature(HookScope $event);

    /**
     * @BeforeScenario
     */
    public static function startScenario(HookScope $event);

    /**
     * @BeforeStep
     */
    public static function startStep(HookScope $event);

    /**
     * @AfterStep
     */
    public static function finishStep(HookScope $event);

    /**
     * @AfterScenario
     */
    public static function finishScenario(HookScope $event);

    /**
     * @AfterFeature
     */
    public static function finishFeature(HookScope $event);

    /**
     * @AfterSuite
     */
    public static function finishLaunch(HookScope $event);
}


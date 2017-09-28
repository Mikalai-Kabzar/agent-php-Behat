<?php

namespace BehatReportPortal;

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
     * @param HookScope $event
     * @return
     */
    public static function startLaunch(HookScope $event);

    /**
     * @BeforeFeature
     * @param HookScope $event
     * @return
     */
    public static function startFeature(HookScope $event);

    /**
     * @BeforeScenario
     * @param HookScope $event
     * @return
     */
    public static function startScenario(HookScope $event);

    /**
     * @BeforeStep
     * @param HookScope $event
     * @return
     */
    public static function startStep(HookScope $event);

    /**
     * @AfterStep
     * @param HookScope $event
     * @return
     */
    public static function finishStep(HookScope $event);

    /**
     * @AfterScenario
     * @param HookScope $event
     * @return
     */
    public static function finishScenario(HookScope $event);

    /**
     * @AfterFeature
     * @param HookScope $event
     * @return
     */
    public static function finishFeature(HookScope $event);

    /**
     * @AfterSuite
     * @param HookScope $event
     * @return
     */
    public static function finishLaunch(HookScope $event);
}


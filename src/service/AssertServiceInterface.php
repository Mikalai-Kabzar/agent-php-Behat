<?php

namespace BehatReportPortal;


interface AssertServiceInterface
{
    /**
     * Get assert message.
     */
    public static function getAssertMessage();

    /**
     * Get stack trace message.
     */
    public static function getStackTraceMessage();
}
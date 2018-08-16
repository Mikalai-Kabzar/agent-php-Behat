# ReportPortal agent for Behat

Specific classes to integrate Behat-baset test framework with Report Portal (http://reportportal.io/).

## How to use.

Use as an example: https://github.com/Mikalai-Kabzar/BDD-Behat-Test-framework

### Steps:

#### 1) Add config.yml file with Report Portal config to your test framework:
```
UUID: 07104d6b-45a0-442f-b7ed-a79fa504a073
host: https://rp.epam.com
projectName: mikalai_kabzar_personal
timeZone: .000+02:00
```
Use as an example: https://github.com/Mikalai-Kabzar/BDD-Behat-Test-framework/blob/master/config.yaml

#### 2) Update your project's composer.json file with next data:

```
	"require" : {
    		...
		"reportportal/behat" : "dev-master",
    		...
	},
```
Use as an example: https://github.com/Mikalai-Kabzar/BDD-Behat-Test-framework/blob/master/composer.json

#### 3) Execute command:
```
            composer update
```

#### 4) Inherit your context files from "BaseFeatureContext" class:

```
<?php
use Behat\Gherkin\Node\TableNode;
use TestFramework\Pages\Application\MainPage;
use TestFramework\Pages\Application\SignInPage;
use TestFramework\Services\AssertService;
use TestFramework\Services\WebElementsService;
/**
 * Defines Application account context.
 */
class AccountContext extends BaseFeatureContext {
    /**
     * @When I hover on My account button
     */
    public function iHoverOnMyAccountButton() {
        MainPage::getMyAccountDropDown()->hoverButton();
    }
	...
```
https://github.com/Mikalai-Kabzar/BDD-Behat-Test-framework/blob/master/features/bootstrap/AccountContext.php
https://github.com/Mikalai-Kabzar/BDD-Behat-Test-framework/blob/master/features/bootstrap/BaseFeatureContext.php

Note: You can implement your own version of "BaseFeatureContext" class.

#### 5) Enjoy

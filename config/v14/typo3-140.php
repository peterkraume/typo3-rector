<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Ssch\TYPO3Rector\TYPO314\v0\DropFifthParameterForExtensionUtilityConfigurePluginRector;
use Ssch\TYPO3Rector\TYPO314\v0\ExtendExtbaseValidatorsFromAbstractValidatorRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../config.php');
    $rectorConfig->rule(DropFifthParameterForExtensionUtilityConfigurePluginRector::class);
    $rectorConfig->rule(ExtendExtbaseValidatorsFromAbstractValidatorRector::class);
};

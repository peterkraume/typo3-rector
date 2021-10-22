<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\Rector\v11\v3;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Type\ObjectType;
use Rector\Core\Console\Output\RectorOutputStyle;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://docs.typo3.org/c/typo3/cms-core/master/en-us/Changelog/11.3/Deprecation-94309-DeprecatedGeneralUtilitystdAuthCode.html
 * @see \Ssch\TYPO3Rector\Tests\Rector\v11\v3\ReplaceStdAuthCodeWithHmacRector\ReplaceStdAuthCodeWithHmacRectorTest
 */
final class ReplaceStdAuthCodeWithHmacRector extends AbstractRector
{
    /**
     * @var string
     */
    private const MESSAGE = 'You have to migrate GeneralUtility::stdAuthCode to GeneralUtility::hmac(). To make types work you should check the old function implementation';

    public function __construct(
        private RectorOutputStyle $rectorOutputStyle
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->nodeTypeResolver->isMethodStaticCallOrClassMethodObjectType(
            $node,
            new ObjectType('TYPO3\CMS\Core\Utility\GeneralUtility')
        )) {
            return null;
        }

        if (! $this->isName($node->name, 'stdAuthCode')) {
            return null;
        }

        $this->rectorOutputStyle->warning(self::MESSAGE);
        return null;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace GeneralUtility::stdAuthCode with GeneralUtility::hmac', [new CodeSample(
            <<<'CODE_SAMPLE'
// Just a warning
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
// Only outputting a warning message
CODE_SAMPLE
        )]);
    }
}
<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\PHPStan\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\FileTypeMapper;
use Rector\Contract\Rector\RectorInterface;
use Ssch\TYPO3Rector\Contract\NoChangelogRequiredInterface;
use Ssch\TYPO3Rector\Rector\AbstractArrayDimFetchTcaRector;
use Ssch\TYPO3Rector\Rector\AbstractTcaRector;

/**
 * @see \Ssch\TYPO3Rector\PHPStan\Tests\Rules\AddChangelogDocBlockForRectorClass\AddChangelogDocBlockForRectorClassTest
 * @implements Rule<Class_>
 */
final class AddChangelogDocBlockForRectorClassRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Provide @changelog doc block for "%s" Rector class';

    /**
     * @var array<class-string<RectorInterface>>
     */
    private const ALLOWED_CLASSES_WITH_NON_CHANGELOG_DOC_BLOCK = [
        AbstractTcaRector::class,
        AbstractArrayDimFetchTcaRector::class,
    ];

    /**
     * @readonly
     */
    private ReflectionProvider $reflectionProvider;

    /**
     * @readonly
     */
    private FileTypeMapper $fileTypeMapper;

    public function __construct(ReflectionProvider $reflectionProvider, FileTypeMapper $fileTypeMapper)
    {
        $this->reflectionProvider = $reflectionProvider;
        $this->fileTypeMapper = $fileTypeMapper;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $className = $node->name;
        if (! $className instanceof Identifier) {
            return [];
        }

        $fullyQualifiedClassName = $scope->getNamespace() . '\\' . $className;

        $classReflection = $this->reflectionProvider->getClass($fullyQualifiedClassName);
        if (! $classReflection->is(RectorInterface::class)) {
            return [];
        }

        if ($classReflection->is(NoChangelogRequiredInterface::class)) {
            return [];
        }

        if (in_array($fullyQualifiedClassName, self::ALLOWED_CLASSES_WITH_NON_CHANGELOG_DOC_BLOCK, true)) {
            return [];
        }

        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return [
                RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $className))
                    ->identifier('change.docblock')
                    ->build(),
            ];
        }

        $resolvedPhpDoc = $this->fileTypeMapper->getResolvedPhpDoc(
            $scope->getFile(),
            $classReflection->getName(),
            null,
            null,
            $docComment->getText()
        );

        $phpDocString = $resolvedPhpDoc->getPhpDocString();
        if (\str_contains($phpDocString, '@changelog')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(self::ERROR_MESSAGE, $className))
                ->identifier('change.docblock')
                ->build(),
        ];
    }
}

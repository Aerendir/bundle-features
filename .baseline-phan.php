<?php
/**
 * This is an automatically generated baseline for Phan issues.
 * When Phan is invoked with --load-baseline=path/to/baseline.php,
 * The pre-existing issues listed in this file won't be emitted.
 *
 * This file can be updated by invoking Phan with --save-baseline=path/to/baseline.php
 * (can be combined with --load-baseline)
 */
return [
    // # Issue statistics:
    // PhanUnreferencedPublicMethod : 30+ occurrences
    // PhanPluginUnreachableCode : 15+ occurrences
    // PhanTypeMismatchReturn : 10+ occurrences
    // PhanUndeclaredMethod : 9 occurrences
    // PhanUnreferencedClosure : 9 occurrences
    // PhanUndeclaredProperty : 8 occurrences
    // PhanCompatiblePHP7 : 6 occurrences
    // PhanTypeMismatchArgument : 5 occurrences
    // PhanTypeMismatchArgumentNullable : 5 occurrences
    // PhanUndeclaredTypeParameter : 5 occurrences
    // PhanUnusedVariableValueOfForeachWithKey : 5 occurrences
    // PhanUnreferencedUseNormal : 4 occurrences
    // PhanTypeMismatchArgumentInternal : 3 occurrences
    // PhanTypeMismatchDeclaredParamNullable : 3 occurrences
    // PhanTypeMismatchDeclaredReturn : 3 occurrences
    // PhanTypeMismatchProperty : 3 occurrences
    // PhanUnusedPublicMethodParameter : 3 occurrences
    // PhanContinueTargetingSwitch : 2 occurrences
    // PhanReadOnlyPrivateProperty : 2 occurrences
    // PhanUnreferencedPrivateProperty : 2 occurrences
    // PhanWriteOnlyPrivateProperty : 2 occurrences
    // ConstReferenceClassNotImported : 1 occurrence
    // PhanDeprecatedFunction : 1 occurrence
    // PhanParamSignatureMismatch : 1 occurrence
    // PhanRedefinedClassReference : 1 occurrence
    // PhanRedefinedExtendedClass : 1 occurrence
    // PhanTypeInvalidLeftOperandOfNumericOp : 1 occurrence
    // PhanTypeMismatchArgumentNullableInternal : 1 occurrence
    // PhanTypeMismatchDimAssignment : 1 occurrence
    // PhanTypeVoidAssignment : 1 occurrence
    // PhanUnextractableAnnotationElementName : 1 occurrence
    // PhanUnextractableAnnotationSuffix : 1 occurrence
    // PhanUnreferencedClass : 1 occurrence
    // PhanUnreferencedPublicClassConstant : 1 occurrence
    // PhanUnusedVariable : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/DependencyInjection/CompilerPass/DrawersCompilerPass.php' => ['PhanUnusedVariableValueOfForeachWithKey'],
        'src/DependencyInjection/CompilerPass/FeaturesManagersCompilerPass.php' => ['PhanUnusedVariableValueOfForeachWithKey'],
        'src/DependencyInjection/CompilerPass/InvoiceManagersCompilerPass.php' => ['PhanUnusedVariableValueOfForeachWithKey'],
        'src/DependencyInjection/Configuration.php' => ['PhanDeprecatedFunction', 'PhanReadOnlyPrivateProperty', 'PhanTypeVoidAssignment', 'PhanUndeclaredMethod', 'PhanUnreferencedClosure', 'PhanUnusedVariable', 'PhanUnusedVariableValueOfForeachWithKey'],
        'src/DependencyInjection/SHQFeaturesExtension.php' => ['PhanTypeMismatchArgumentNullable', 'PhanUnreferencedClass'],
        'src/FeaturesFactory.php' => ['PhanPluginUnreachableCode'],
        'src/Form/DataTransformer/BooleanFeatureTransformer.php' => ['PhanTypeMismatchReturn', 'PhanUndeclaredMethod'],
        'src/Form/DataTransformer/CountableFeatureTransformer.php' => ['PhanTypeMismatchReturn', 'PhanUndeclaredMethod'],
        'src/Form/DataTransformer/RechargeableFeatureTransformer.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchReturn', 'PhanUndeclaredMethod', 'PhanUnusedPublicMethodParameter'],
        'src/Form/Type/FeaturesType.php' => ['PhanTypeMismatchArgumentNullable', 'PhanUnreferencedClosure'],
        'src/InvoiceDrawer/AbstractInvoiceDrawer.php' => ['PhanUndeclaredProperty', 'PhanUnreferencedPublicMethod'],
        'src/InvoiceDrawer/InvoiceDrawerInterface.php' => ['PhanTypeMismatchDeclaredReturn'],
        'src/InvoiceDrawer/PlainTextDrawer.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentInternal', 'PhanUnreferencedPublicMethod'],
        'src/Model/AbstractFeaturesCollection.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanUnreferencedClosure', 'PhanUnreferencedPublicClassConstant', 'PhanUnreferencedPublicMethod'],
        'src/Model/ConfiguredCountableFeature.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnreferencedPublicMethod', 'PhanUnusedPublicMethodParameter'],
        'src/Model/ConfiguredRechargeableFeature.php' => ['PhanUnusedPublicMethodParameter'],
        'src/Model/Invoice.php' => ['PhanTypeMismatchArgumentInternal', 'PhanTypeMismatchArgumentNullable', 'PhanTypeMismatchArgumentNullableInternal', 'PhanTypeMismatchDimAssignment'],
        'src/Model/InvoiceInterface.php' => ['PhanTypeMismatchDeclaredParamNullable', 'PhanUnextractableAnnotationElementName', 'PhanUnextractableAnnotationSuffix'],
        'src/Model/InvoiceLine.php' => ['PhanTypeMismatchProperty', 'PhanTypeMismatchReturn'],
        'src/Model/InvoiceSection.php' => ['PhanTypeMismatchProperty'],
        'src/Model/InvoiceSectionHeader.php' => ['PhanUnreferencedPublicMethod'],
        'src/Model/SubscribedCountableFeature.php' => ['PhanParamSignatureMismatch', 'PhanPluginUnreachableCode', 'PhanUndeclaredMethod'],
        'src/Model/SubscribedCountableFeatureInterface.php' => ['ConstReferenceClassNotImported'],
        'src/Model/SubscribedRechargeableFeature.php' => ['PhanWriteOnlyPrivateProperty'],
        'src/Model/Subscription.php' => ['PhanTypeMismatchArgument', 'PhanUnreferencedPublicMethod'],
        'src/Property/CanBeConsumedProperty.php' => ['PhanTypeMismatchReturn', 'PhanUnreferencedPublicMethod'],
        'src/Property/CanBeFreeProperty.php' => ['PhanUndeclaredProperty', 'PhanUnreferencedPublicMethod'],
        'src/Property/CanHaveFreePackProperty.php' => ['PhanTypeMismatchReturn', 'PhanUnreferencedPublicMethod'],
        'src/Property/HasConfiguredPacksProperty.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchReturn', 'PhanUndeclaredProperty'],
        'src/Property/HasRecurringPricesInterface.php' => ['PhanTypeMismatchDeclaredReturn', 'PhanUndeclaredTypeParameter'],
        'src/Property/HasRecurringPricesProperty.php' => ['PhanCompatiblePHP7', 'PhanPluginUnreachableCode', 'PhanTypeInvalidLeftOperandOfNumericOp', 'PhanTypeMismatchDeclaredReturn', 'PhanTypeMismatchReturn', 'PhanUndeclaredProperty', 'PhanUnreferencedPrivateProperty', 'PhanUnreferencedPublicMethod'],
        'src/Property/HasUnatantumPricesInterface.php' => ['PhanUndeclaredTypeParameter'],
        'src/Property/HasUnatantumPricesProperty.php' => ['PhanCompatiblePHP7', 'PhanPluginUnreachableCode', 'PhanTypeMismatchReturn', 'PhanUnreferencedPublicMethod'],
        'src/Property/IsRecurringFeatureProperty.php' => ['PhanTypeMismatchReturn', 'PhanUnreferencedPublicMethod'],
        'src/Service/FeaturesManager.php' => ['PhanContinueTargetingSwitch', 'PhanPluginUnreachableCode', 'PhanUnreferencedPublicMethod', 'PhanUnreferencedUseNormal'],
        'src/Service/InvoicesManager.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchDeclaredParamNullable', 'PhanTypeMismatchProperty', 'PhanUndeclaredMethod', 'PhanUnreferencedPublicMethod', 'PhanUnreferencedUseNormal', 'PhanWriteOnlyPrivateProperty'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

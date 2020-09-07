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
    // PhanPluginUnreachableCode : 15+ occurrences
    // PhanUnreferencedClosure : 9 occurrences
    // PhanTypeMismatchReturn : 8 occurrences
    // PhanTypeMismatchArgument : 7 occurrences
    // PhanCompatiblePHP7 : 6 occurrences
    // PhanUndeclaredMethod : 6 occurrences
    // PhanTypeMismatchArgumentNullable : 5 occurrences
    // PhanTypeMismatchDeclaredReturn : 4 occurrences
    // PhanUndeclaredProperty : 4 occurrences
    // PhanTypeMismatchArgumentInternal : 3 occurrences
    // PhanTypeMismatchDeclaredParamNullable : 3 occurrences
    // PhanUnusedPublicFinalMethodParameter : 3 occurrences
    // PhanWriteOnlyPrivateProperty : 3 occurrences
    // PhanTypeMismatchDimAssignment : 2 occurrences
    // PhanTypeMismatchPropertyProbablyReal : 2 occurrences
    // PhanUndeclaredTypeParameter : 2 occurrences
    // ConstReferenceClassNotImported : 1 occurrence
    // PhanParamSignatureMismatch : 1 occurrence
    // PhanReadOnlyPrivateProperty : 1 occurrence
    // PhanRedefinedClassReference : 1 occurrence
    // PhanRedefinedExtendedClass : 1 occurrence
    // PhanTypeInvalidLeftOperandOfNumericOp : 1 occurrence
    // PhanTypeMismatchProperty : 1 occurrence
    // PhanTypeMismatchReturnNullable : 1 occurrence
    // PhanUndeclaredTypeReturnType : 1 occurrence
    // PhanUnextractableAnnotationElementName : 1 occurrence
    // PhanUnextractableAnnotationSuffix : 1 occurrence
    // PhanUnreferencedClass : 1 occurrence
    // PhanUnreferencedPublicClassConstant : 1 occurrence
    // PhanWriteOnlyPublicProperty : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/DependencyInjection/Configuration.php' => ['PhanUndeclaredMethod', 'PhanUnreferencedClosure'],
        'src/DependencyInjection/SHQFeaturesExtension.php' => ['PhanUnreferencedClass'],
        'src/FeaturesFactory.php' => ['PhanPluginUnreachableCode'],
        'src/Form/DataTransformer/BooleanFeatureTransformer.php' => ['PhanUndeclaredMethod'],
        'src/Form/DataTransformer/CountableFeatureTransformer.php' => ['PhanUndeclaredMethod'],
        'src/Form/DataTransformer/RechargeableFeatureTransformer.php' => ['PhanTypeMismatchArgument', 'PhanUndeclaredMethod', 'PhanUnusedPublicFinalMethodParameter'],
        'src/Form/Type/FeaturesType.php' => ['PhanTypeMismatchArgumentNullable', 'PhanUnreferencedClosure'],
        'src/InvoiceDrawer/AbstractInvoiceDrawer.php' => ['PhanWriteOnlyPublicProperty'],
        'src/InvoiceDrawer/InvoiceDrawerInterface.php' => ['PhanTypeMismatchDeclaredReturn'],
        'src/InvoiceDrawer/PlainTextDrawer.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentInternal'],
        'src/Manager/FeaturesManager.php' => ['PhanTypeMismatchDeclaredParamNullable'],
        'src/Manager/InvoicesManager.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentNullable', 'PhanTypeMismatchDeclaredParamNullable', 'PhanTypeMismatchDeclaredReturn', 'PhanTypeMismatchPropertyProbablyReal', 'PhanWriteOnlyPrivateProperty'],
        'src/Model/AbstractFeaturesCollection.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUndeclaredTypeReturnType', 'PhanUnreferencedClosure', 'PhanUnreferencedPublicClassConstant'],
        'src/Model/ConfiguredCountableFeature.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnusedPublicFinalMethodParameter'],
        'src/Model/ConfiguredRechargeableFeature.php' => ['PhanUnusedPublicFinalMethodParameter'],
        'src/Model/Invoice.php' => ['PhanTypeMismatchArgumentInternal', 'PhanTypeMismatchArgumentNullable', 'PhanTypeMismatchDimAssignment', 'PhanTypeMismatchReturnNullable'],
        'src/Model/InvoiceInterface.php' => ['PhanUnextractableAnnotationElementName', 'PhanUnextractableAnnotationSuffix'],
        'src/Model/InvoiceLine.php' => ['PhanTypeMismatchDeclaredReturn', 'PhanTypeMismatchProperty'],
        'src/Model/InvoiceSection.php' => ['PhanTypeMismatchArgumentNullable', 'PhanTypeMismatchDimAssignment', 'PhanTypeMismatchPropertyProbablyReal'],
        'src/Model/Property/CanBeConsumedProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Property/CanBeFreeProperty.php' => ['PhanUndeclaredProperty'],
        'src/Model/Property/CanHaveFreePackProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Property/HasConfiguredPacksProperty.php' => ['PhanTypeMismatchReturn', 'PhanUndeclaredProperty'],
        'src/Model/Property/HasRecurringPricesProperty.php' => ['PhanCompatiblePHP7', 'PhanPluginUnreachableCode', 'PhanTypeInvalidLeftOperandOfNumericOp', 'PhanTypeMismatchReturn', 'PhanWriteOnlyPrivateProperty'],
        'src/Model/Property/HasUnatantumPricesInterface.php' => ['PhanUndeclaredTypeParameter'],
        'src/Model/Property/HasUnatantumPricesProperty.php' => ['PhanCompatiblePHP7', 'PhanPluginUnreachableCode', 'PhanTypeMismatchReturn'],
        'src/Model/Property/IsRecurringFeatureProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/SubscribedCountableFeature.php' => ['PhanParamSignatureMismatch', 'PhanPluginUnreachableCode'],
        'src/Model/SubscribedCountableFeatureInterface.php' => ['ConstReferenceClassNotImported'],
        'src/Model/SubscribedRechargeableFeature.php' => ['PhanWriteOnlyPrivateProperty'],
        'src/Model/Subscription.php' => ['PhanTypeMismatchArgument'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

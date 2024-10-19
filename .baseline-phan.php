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
    // PhanDeprecatedFunction : 20+ occurrences
    // PhanUndeclaredMethod : 15+ occurrences
    // PhanParamSignatureMismatch : 10+ occurrences
    // PhanTypeMismatchReturn : 10+ occurrences
    // PhanUnreferencedClosure : 9 occurrences
    // PhanTypeMismatchArgument : 8 occurrences
    // PhanUndeclaredProperty : 8 occurrences
    // PhanTypeMismatchDeclaredReturn : 6 occurrences
    // PhanUnreferencedProtectedMethod : 6 occurrences
    // PhanTypeMismatchReturnSuperType : 5 occurrences
    // PhanTypeMismatchArgumentSuperType : 4 occurrences
    // PhanTypeMismatchArgumentNullable : 3 occurrences
    // PhanUnusedPublicFinalMethodParameter : 3 occurrences
    // PhanWriteOnlyPrivateProperty : 3 occurrences
    // PhanCompatibleUnionType : 2 occurrences
    // PhanTypeArraySuspicious : 2 occurrences
    // PhanTypeMismatchDimAssignment : 2 occurrences
    // PhanUndeclaredTypeParameter : 2 occurrences
    // PhanUnusedVariable : 2 occurrences
    // ConstReferenceClassNotImported : 1 occurrence
    // PhanAccessClassInternal : 1 occurrence
    // PhanCompatiblePHP7 : 1 occurrence
    // PhanPossiblyNullTypeMismatchProperty : 1 occurrence
    // PhanReadOnlyPrivateProperty : 1 occurrence
    // PhanTypeInvalidLeftOperandOfNumericOp : 1 occurrence
    // PhanTypeMismatchArgumentInternal : 1 occurrence
    // PhanTypeMismatchPropertyReal : 1 occurrence
    // PhanTypeMismatchReturnNullable : 1 occurrence
    // PhanUnextractableAnnotationElementName : 1 occurrence
    // PhanUnextractableAnnotationSuffix : 1 occurrence
    // PhanUnreferencedClass : 1 occurrence
    // PhanUnreferencedPrivateProperty : 1 occurrence
    // PhanUnreferencedPublicClassConstant : 1 occurrence
    // PhanWriteOnlyPublicProperty : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/DependencyInjection/Configuration.php' => ['PhanDeprecatedFunction', 'PhanUndeclaredMethod', 'PhanUnreferencedClosure', 'PhanUnusedVariable'],
        'src/DependencyInjection/SHQFeaturesExtension.php' => ['PhanAccessClassInternal', 'PhanUnreferencedClass'],
        'src/Form/DataTransformer/AbstractFeatureTransformer.php' => ['PhanDeprecatedFunction'],
        'src/Form/DataTransformer/BooleanFeatureTransformer.php' => ['PhanParamSignatureMismatch', 'PhanTypeMismatchReturnSuperType', 'PhanUndeclaredMethod'],
        'src/Form/DataTransformer/CountableFeatureTransformer.php' => ['PhanParamSignatureMismatch', 'PhanTypeMismatchReturnSuperType', 'PhanUndeclaredMethod'],
        'src/Form/DataTransformer/FeaturesCollectionTransformer.php' => ['PhanParamSignatureMismatch'],
        'src/Form/DataTransformer/RechargeableFeatureTransformer.php' => ['PhanParamSignatureMismatch', 'PhanTypeMismatchArgument', 'PhanTypeMismatchReturnSuperType', 'PhanUndeclaredMethod', 'PhanUnusedPublicFinalMethodParameter'],
        'src/Form/Type/FeaturesType.php' => ['PhanTypeMismatchArgumentSuperType', 'PhanUndeclaredMethod', 'PhanUnreferencedClosure'],
        'src/InvoiceDrawer/AbstractInvoiceDrawer.php' => ['PhanWriteOnlyPublicProperty'],
        'src/Manager/FeaturesManager.php' => ['PhanDeprecatedFunction', 'PhanUndeclaredMethod'],
        'src/Manager/InvoicesManager.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentNullable', 'PhanUndeclaredMethod', 'PhanWriteOnlyPrivateProperty'],
        'src/Model/Feature/AbstractFeaturesCollection.php' => ['PhanDeprecatedFunction', 'PhanUnreferencedClosure', 'PhanUnreferencedPublicClassConstant'],
        'src/Model/Feature/Configured/ConfiguredCountableFeature.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnusedPublicFinalMethodParameter'],
        'src/Model/Feature/Configured/ConfiguredFeaturesCollection.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchDeclaredReturn', 'PhanUnreferencedProtectedMethod'],
        'src/Model/Feature/Configured/ConfiguredRechargeableFeature.php' => ['PhanUnusedPublicFinalMethodParameter'],
        'src/Model/Feature/Property/CanBeConsumedProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Feature/Property/CanBeEnabledProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Feature/Property/CanBeFreeProperty.php' => ['PhanUndeclaredProperty', 'PhanUnusedVariable'],
        'src/Model/Feature/Property/CanHaveFreePackProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Feature/Property/HasConfiguredPacksProperty.php' => ['PhanDeprecatedFunction', 'PhanTypeMismatchReturn', 'PhanUndeclaredProperty'],
        'src/Model/Feature/Property/HasRecurringPricesProperty.php' => ['PhanDeprecatedFunction', 'PhanTypeInvalidLeftOperandOfNumericOp', 'PhanTypeMismatchReturn', 'PhanWriteOnlyPrivateProperty'],
        'src/Model/Feature/Property/HasUnatantumPricesInterface.php' => ['PhanUndeclaredTypeParameter'],
        'src/Model/Feature/Property/HasUnatantumPricesProperty.php' => ['PhanCompatiblePHP7', 'PhanDeprecatedFunction', 'PhanTypeMismatchReturn'],
        'src/Model/Feature/Property/IsRecurringFeatureProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Feature/Subscribed/SubscribedBooleanFeature.php' => ['PhanUnreferencedPrivateProperty'],
        'src/Model/Feature/Subscribed/SubscribedCountableFeature.php' => ['ConstReferenceClassNotImported', 'PhanUndeclaredMethod'],
        'src/Model/Feature/Subscribed/SubscribedFeaturesCollection.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchDeclaredReturn', 'PhanUnreferencedProtectedMethod'],
        'src/Model/Feature/Subscribed/SubscribedRechargeableFeature.php' => ['PhanCompatibleUnionType', 'PhanTypeMismatchReturnSuperType', 'PhanWriteOnlyPrivateProperty'],
        'src/Model/Invoice.php' => ['PhanDeprecatedFunction', 'PhanParamSignatureMismatch', 'PhanTypeArraySuspicious', 'PhanTypeMismatchArgumentInternal', 'PhanTypeMismatchArgumentNullable', 'PhanTypeMismatchDimAssignment', 'PhanTypeMismatchReturn', 'PhanTypeMismatchReturnNullable'],
        'src/Model/InvoiceInterface.php' => ['PhanUnextractableAnnotationElementName', 'PhanUnextractableAnnotationSuffix'],
        'src/Model/InvoiceLine.php' => ['PhanPossiblyNullTypeMismatchProperty'],
        'src/Model/InvoiceSection.php' => ['PhanDeprecatedFunction', 'PhanTypeMismatchArgumentNullable', 'PhanTypeMismatchDimAssignment', 'PhanTypeMismatchPropertyReal'],
        'src/Model/Subscription.php' => ['PhanCompatibleUnionType', 'PhanDeprecatedFunction', 'PhanParamSignatureMismatch', 'PhanTypeMismatchReturnSuperType'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

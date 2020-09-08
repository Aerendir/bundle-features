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
    // PhanTypeMismatchReturn : 10+ occurrences
    // PhanUnreferencedClosure : 9 occurrences
    // PhanTypeMismatchArgument : 6 occurrences
    // PhanUndeclaredMethod : 6 occurrences
    // PhanTypeMismatchDeclaredReturn : 4 occurrences
    // PhanUndeclaredProperty : 4 occurrences
    // PhanCompatiblePHP7 : 3 occurrences
    // PhanTypeMismatchArgumentInternal : 3 occurrences
    // PhanTypeMismatchArgumentNullable : 3 occurrences
    // PhanUnusedPublicFinalMethodParameter : 3 occurrences
    // PhanWriteOnlyPrivateProperty : 3 occurrences
    // PhanTypeInvalidPropertyName : 2 occurrences
    // PhanTypeMismatchDimAssignment : 2 occurrences
    // PhanTypeMismatchPropertyProbablyReal : 2 occurrences
    // PhanUndeclaredTypeParameter : 2 occurrences
    // ConstReferenceClassNotImported : 1 occurrence
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
    // PhanUnreferencedPrivateProperty : 1 occurrence
    // PhanUnreferencedPublicClassConstant : 1 occurrence
    // PhanWriteOnlyPublicProperty : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/DependencyInjection/Configuration.php' => ['PhanUndeclaredMethod', 'PhanUnreferencedClosure'],
        'src/DependencyInjection/SHQFeaturesExtension.php' => ['PhanUnreferencedClass'],
        'src/Form/DataTransformer/BooleanFeatureTransformer.php' => ['PhanUndeclaredMethod'],
        'src/Form/DataTransformer/CountableFeatureTransformer.php' => ['PhanUndeclaredMethod'],
        'src/Form/DataTransformer/RechargeableFeatureTransformer.php' => ['PhanTypeMismatchArgument', 'PhanUndeclaredMethod', 'PhanUnusedPublicFinalMethodParameter'],
        'src/Form/Type/FeaturesType.php' => ['PhanUnreferencedClosure'],
        'src/InvoiceDrawer/AbstractInvoiceDrawer.php' => ['PhanWriteOnlyPublicProperty'],
        'src/InvoiceDrawer/InvoiceDrawerInterface.php' => ['PhanTypeMismatchDeclaredReturn'],
        'src/InvoiceDrawer/PlainTextDrawer.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentInternal'],
        'src/Manager/InvoicesManager.php' => ['PhanTypeMismatchArgument', 'PhanTypeMismatchArgumentNullable', 'PhanTypeMismatchDeclaredReturn', 'PhanTypeMismatchPropertyProbablyReal', 'PhanWriteOnlyPrivateProperty'],
        'src/Model/Feature/AbstractFeaturesCollection.php' => ['PhanRedefinedClassReference', 'PhanRedefinedExtendedClass', 'PhanTypeMismatchArgument', 'PhanUndeclaredTypeReturnType', 'PhanUnreferencedClosure', 'PhanUnreferencedPublicClassConstant'],
        'src/Model/Feature/Configured/ConfiguredCountableFeature.php' => ['PhanReadOnlyPrivateProperty', 'PhanUnusedPublicFinalMethodParameter'],
        'src/Model/Feature/Configured/ConfiguredRechargeableFeature.php' => ['PhanUnusedPublicFinalMethodParameter'],
        'src/Model/Feature/Property/CanBeConsumedProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Feature/Property/CanBeEnabledProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Feature/Property/CanBeFreeProperty.php' => ['PhanUndeclaredProperty'],
        'src/Model/Feature/Property/CanHaveFreePackProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Feature/Property/HasConfiguredPacksProperty.php' => ['PhanTypeMismatchReturn', 'PhanUndeclaredProperty'],
        'src/Model/Feature/Property/HasRecurringPricesProperty.php' => ['PhanCompatiblePHP7', 'PhanTypeInvalidLeftOperandOfNumericOp', 'PhanTypeInvalidPropertyName', 'PhanTypeMismatchReturn', 'PhanWriteOnlyPrivateProperty'],
        'src/Model/Feature/Property/HasUnatantumPricesInterface.php' => ['PhanUndeclaredTypeParameter'],
        'src/Model/Feature/Property/HasUnatantumPricesProperty.php' => ['PhanCompatiblePHP7', 'PhanTypeMismatchReturn'],
        'src/Model/Feature/Property/IsRecurringFeatureProperty.php' => ['PhanTypeMismatchReturn'],
        'src/Model/Feature/Subscribed/SubscribedBooleanFeature.php' => ['PhanUnreferencedPrivateProperty'],
        'src/Model/Feature/Subscribed/SubscribedCountableFeature.php' => ['ConstReferenceClassNotImported'],
        'src/Model/Feature/Subscribed/SubscribedRechargeableFeature.php' => ['PhanWriteOnlyPrivateProperty'],
        'src/Model/Invoice.php' => ['PhanTypeMismatchArgumentInternal', 'PhanTypeMismatchArgumentNullable', 'PhanTypeMismatchDimAssignment', 'PhanTypeMismatchReturnNullable'],
        'src/Model/InvoiceInterface.php' => ['PhanUnextractableAnnotationElementName', 'PhanUnextractableAnnotationSuffix'],
        'src/Model/InvoiceLine.php' => ['PhanTypeMismatchDeclaredReturn', 'PhanTypeMismatchProperty'],
        'src/Model/InvoiceSection.php' => ['PhanTypeMismatchArgumentNullable', 'PhanTypeMismatchDimAssignment', 'PhanTypeMismatchPropertyProbablyReal'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

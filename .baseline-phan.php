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
    // PhanUndeclaredMethod : 15+ occurrences
    // PhanParamSignatureMismatch : 10+ occurrences
    // PhanTypeMismatchReturn : 10+ occurrences
    // PhanUnreferencedClosure : 9 occurrences
    // PhanTypeMismatchArgument : 8 occurrences
    // PhanTypeMismatchDeclaredReturn : 6 occurrences
    // PhanTypeMismatchReturnSuperType : 6 occurrences
    // PhanUndeclaredProperty : 6 occurrences
    // PhanUnreferencedProtectedMethod : 6 occurrences
    // PhanDeprecatedClassConstant : 4 occurrences
    // PhanTypeMismatchArgumentSuperType : 4 occurrences
    // PhanTypeArraySuspicious : 3 occurrences
    // PhanTypeMismatchArgumentNullable : 3 occurrences
    // PhanUnusedPublicFinalMethodParameter : 3 occurrences
    // PhanGenericMissingParameters : 2 occurrences
    // PhanTypeMismatchArgumentNullableInternal : 2 occurrences
    // PhanTypeMismatchDimAssignment : 2 occurrences
    // PhanUndeclaredTypeParameter : 2 occurrences
    // PhanUnusedVariable : 2 occurrences
    // PhanWriteOnlyPrivateProperty : 2 occurrences
    // ConstReferenceClassNotImported : 1 occurrence
    // PhanPossiblyNullTypeMismatchProperty : 1 occurrence
    // PhanReadOnlyPrivateProperty : 1 occurrence
    // PhanTypeInvalidLeftOperandOfNumericOp : 1 occurrence
    // PhanTypeMismatchProperty : 1 occurrence
    // PhanTypeMismatchPropertyDefault : 1 occurrence
    // PhanTypeMismatchPropertyReal : 1 occurrence
    // PhanTypeMismatchReturnNullable : 1 occurrence
    // PhanUndeclaredTypeProperty : 1 occurrence
    // PhanUnextractableAnnotationElementName : 1 occurrence
    // PhanUnextractableAnnotationSuffix : 1 occurrence
    // PhanUnreferencedClass : 1 occurrence
    // PhanUnreferencedPrivateProperty : 1 occurrence
    // PhanUnreferencedPublicClassConstant : 1 occurrence
    // PhanWriteOnlyPublicProperty : 1 occurrence
    // UndeclaredTypeInInlineVar : 1 occurrence

    'file_suppressions' => [
        'src/DependencyInjection/Configuration.php' => [
            'PhanTypeArraySuspicious' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\DependencyInjection\\Configuration::processSets'],
            'PhanTypeMismatchProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\DependencyInjection\\Configuration::processSets'],
            'PhanTypeMismatchPropertyDefault' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\DependencyInjection\\Configuration'],
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\DependencyInjection\\Configuration::getConfigTreeBuilder'],
            'PhanUndeclaredTypeProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\DependencyInjection\\Configuration'],
            'PhanUnreferencedClosure' => ['\\closure'],
            'PhanUnusedVariable' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\DependencyInjection\\Configuration::recurringFeatureHasFreePackage'],
            'UndeclaredTypeInInlineVar' => ['src/DependencyInjection/Configuration.php']
        ],
        'src/DependencyInjection/SHQFeaturesExtension.php' => [
            'PhanUnreferencedClass' => ['src/DependencyInjection/SHQFeaturesExtension.php']
        ],
        'src/Form/DataTransformer/AbstractFeatureTransformer.php' => [
            'PhanGenericMissingParameters' => ['src/Form/DataTransformer/AbstractFeatureTransformer.php']
        ],
        'src/Form/DataTransformer/BooleanFeatureTransformer.php' => [
            'PhanParamSignatureMismatch' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\BooleanFeatureTransformer::reverseTransform', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\BooleanFeatureTransformer::transform'],
            'PhanTypeMismatchReturnSuperType' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\BooleanFeatureTransformer::reverseTransform'],
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\BooleanFeatureTransformer::reverseTransform']
        ],
        'src/Form/DataTransformer/CountableFeatureTransformer.php' => [
            'PhanParamSignatureMismatch' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\CountableFeatureTransformer::reverseTransform', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\CountableFeatureTransformer::transform'],
            'PhanTypeMismatchReturnSuperType' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\CountableFeatureTransformer::reverseTransform'],
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\CountableFeatureTransformer::reverseTransform']
        ],
        'src/Form/DataTransformer/FeaturesCollectionTransformer.php' => [
            'PhanGenericMissingParameters' => ['src/Form/DataTransformer/FeaturesCollectionTransformer.php'],
            'PhanParamSignatureMismatch' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\FeaturesCollectionTransformer::reverseTransform', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\FeaturesCollectionTransformer::transform']
        ],
        'src/Form/DataTransformer/RechargeableFeatureTransformer.php' => [
            'PhanParamSignatureMismatch' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\RechargeableFeatureTransformer::reverseTransform', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\RechargeableFeatureTransformer::transform'],
            'PhanTypeMismatchArgument' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\RechargeableFeatureTransformer::reverseTransform'],
            'PhanTypeMismatchReturnSuperType' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\RechargeableFeatureTransformer::reverseTransform'],
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\RechargeableFeatureTransformer::reverseTransform'],
            'PhanUnusedPublicFinalMethodParameter' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\DataTransformer\\RechargeableFeatureTransformer::transform']
        ],
        'src/Form/Type/FeaturesType.php' => [
            'PhanTypeMismatchArgumentSuperType' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\Type\\FeaturesType::getCountableFeaturePacksOptions', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\Type\\FeaturesType::getRechargeableFeaturePacksOptions'],
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Form\\Type\\FeaturesType::getBooleanFeatureOptions'],
            'PhanUnreferencedClosure' => ['\\closure']
        ],
        'src/InvoiceDrawer/AbstractInvoiceDrawer.php' => [
            'PhanWriteOnlyPublicProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\InvoiceDrawer\\AbstractInvoiceDrawer']
        ],
        'src/Manager/FeaturesManager.php' => [
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Manager\\FeaturesManager::refreshCountableFeatures']
        ],
        'src/Manager/InvoicesManager.php' => [
            'PhanTypeMismatchArgument' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Manager\\InvoicesManager::populateSection'],
            'PhanTypeMismatchArgumentNullable' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Manager\\InvoicesManager::populateInvoice'],
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Manager\\InvoicesManager::populateSection'],
            'PhanWriteOnlyPrivateProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Manager\\InvoicesManager']
        ],
        'src/Model/Feature/AbstractFeaturesCollection.php' => [
            'PhanUnreferencedClosure' => ['\\closure'],
            'PhanUnreferencedPublicClassConstant' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\AbstractFeaturesCollection']
        ],
        'src/Model/Feature/Configured/ConfiguredCountableFeature.php' => [
            'PhanReadOnlyPrivateProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredCountableFeature'],
            'PhanUnusedPublicFinalMethodParameter' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredCountableFeature::setPacks']
        ],
        'src/Model/Feature/Configured/ConfiguredFeaturesCollection.php' => [
            'PhanTypeMismatchArgument' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredFeaturesCollection::getBooleanFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredFeaturesCollection::getCountableFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredFeaturesCollection::getRechargeableFeatures'],
            'PhanTypeMismatchDeclaredReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredFeaturesCollection::getBooleanFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredFeaturesCollection::getCountableFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredFeaturesCollection::getRechargeableFeatures'],
            'PhanUnreferencedProtectedMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredFeaturesCollection::getBooleanFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredFeaturesCollection::getCountableFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredFeaturesCollection::getRechargeableFeatures']
        ],
        'src/Model/Feature/Configured/ConfiguredRechargeableFeature.php' => [
            'PhanUnusedPublicFinalMethodParameter' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Configured\\ConfiguredRechargeableFeature::setPacks']
        ],
        'src/Model/Feature/Property/CanBeConsumedProperty.php' => [
            'PhanTypeMismatchReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\CanBeConsumedProperty::consume', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\CanBeConsumedProperty::setRemainedQuantity']
        ],
        'src/Model/Feature/Property/CanBeEnabledProperty.php' => [
            'PhanTypeMismatchReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\CanBeEnabledProperty::disable', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\CanBeEnabledProperty::enable']
        ],
        'src/Model/Feature/Property/CanBeFreeProperty.php' => [
            'PhanUndeclaredProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\CanBeFreeProperty::isFree'],
            'PhanUnusedVariable' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\CanBeFreeProperty::isFree']
        ],
        'src/Model/Feature/Property/CanHaveFreePackProperty.php' => [
            'PhanTypeMismatchReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\CanHaveFreePackProperty::setFreePack']
        ],
        'src/Model/Feature/Property/HasConfiguredPacksProperty.php' => [
            'PhanTypeMismatchReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\HasConfiguredPacksProperty::setPacks'],
            'PhanUndeclaredProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\HasConfiguredPacksProperty::setPacks']
        ],
        'src/Model/Feature/Property/HasRecurringPricesProperty.php' => [
            'PhanTypeInvalidLeftOperandOfNumericOp' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\HasRecurringPricesProperty::calculateInstantPrice'],
            'PhanTypeMismatchReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\HasRecurringPricesProperty::setSubscription', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\HasRecurringPricesProperty::setTax'],
            'PhanWriteOnlyPrivateProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\HasRecurringPricesProperty']
        ],
        'src/Model/Feature/Property/HasUnatantumPricesInterface.php' => [
            'PhanUndeclaredTypeParameter' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\HasUnatantumPricesInterface::getPrice', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\HasUnatantumPricesInterface::hasPrice']
        ],
        'src/Model/Feature/Property/HasUnatantumPricesProperty.php' => [
            'PhanTypeMismatchReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\HasUnatantumPricesProperty::setTax']
        ],
        'src/Model/Feature/Property/IsRecurringFeatureProperty.php' => [
            'PhanTypeMismatchReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Property\\IsRecurringFeatureProperty::setActiveUntil']
        ],
        'src/Model/Feature/Subscribed/SubscribedBooleanFeature.php' => [
            'PhanUnreferencedPrivateProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedBooleanFeature']
        ],
        'src/Model/Feature/Subscribed/SubscribedCountableFeature.php' => [
            'ConstReferenceClassNotImported' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedCountableFeature::getSubscribedPack'],
            'PhanUndeclaredMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedCountableFeature::isRefreshPeriodElapsed']
        ],
        'src/Model/Feature/Subscribed/SubscribedFeaturesCollection.php' => [
            'PhanTypeMismatchArgument' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedFeaturesCollection::getBooleanFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedFeaturesCollection::getCountableFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedFeaturesCollection::getRechargeableFeatures'],
            'PhanTypeMismatchDeclaredReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedFeaturesCollection::getBooleanFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedFeaturesCollection::getCountableFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedFeaturesCollection::getRechargeableFeatures'],
            'PhanUnreferencedProtectedMethod' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedFeaturesCollection::getBooleanFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedFeaturesCollection::getCountableFeatures', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedFeaturesCollection::getRechargeableFeatures']
        ],
        'src/Model/Feature/Subscribed/SubscribedRechargeableFeature.php' => [
            'PhanTypeMismatchReturnSuperType' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Feature\\Subscribed\\SubscribedRechargeableFeature::getLastRechargeOn']
        ],
        'src/Model/Invoice.php' => [
            'PhanDeprecatedClassConstant' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Invoice'],
            'PhanParamSignatureMismatch' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Invoice::removeHeader'],
            'PhanTypeArraySuspicious' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Invoice::jsonUnserialize'],
            'PhanTypeMismatchArgumentNullable' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Invoice::addSection'],
            'PhanTypeMismatchArgumentNullableInternal' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Invoice::addSection'],
            'PhanTypeMismatchDimAssignment' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Invoice::addSection'],
            'PhanTypeMismatchReturn' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Invoice::removeSection'],
            'PhanTypeMismatchReturnNullable' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Invoice::getLine'],
            'PhanTypeMismatchReturnSuperType' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Invoice::getIssuedOn']
        ],
        'src/Model/InvoiceInterface.php' => [
            'PhanUnextractableAnnotationElementName' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\InvoiceInterface'],
            'PhanUnextractableAnnotationSuffix' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\InvoiceInterface']
        ],
        'src/Model/InvoiceLine.php' => [
            'PhanPossiblyNullTypeMismatchProperty' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\InvoiceLine::setQuantity']
        ],
        'src/Model/InvoiceSection.php' => [
            'PhanTypeMismatchArgumentNullable' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\InvoiceSection::addLine'],
            'PhanTypeMismatchArgumentNullableInternal' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\InvoiceSection::addLine'],
            'PhanTypeMismatchDimAssignment' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\InvoiceSection::addLine'],
            'PhanTypeMismatchPropertyReal' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\InvoiceSection::removeHeader']
        ],
        'src/Model/Subscription.php' => [
            'PhanDeprecatedClassConstant' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Subscription'],
            'PhanParamSignatureMismatch' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Subscription::setNextRefreshOn', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Subscription::setNextRenewOn', '\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Subscription::setSubscribedOn'],
            'PhanTypeMismatchReturnSuperType' => ['\\SerendipityHQ\\Bundle\\FeaturesBundle\\Model\\Subscription::getNextRefreshOn']
        ],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];

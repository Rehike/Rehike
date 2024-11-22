<?php
namespace Rehike\Model\ViewModelConverter;

/**
 * Provides basic functionality common to most view model converters.
 * 
 * @author The Rehike Maintainers
 */
abstract class BasicVMC
{
    protected object $viewModel;
    protected object $frameworkUpdates;

    public function __construct(object $viewModel, object $frameworkUpdates)
    {
        $this->viewModel = $viewModel;
        $this->frameworkUpdates = $frameworkUpdates;
    }
}
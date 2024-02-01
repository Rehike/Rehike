<?php
namespace Rehike\Model\ViewModelConverter;

/**
 * 
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
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
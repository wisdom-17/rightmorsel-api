<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;


class AppBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);
        // adding a custom compiler pass to change services to public when testing. 

        // See more: https://www.tomasvotruba.cz/blog/2018/05/17/how-to-test-private-services-in-symfony/#down-the-smelly-rabbit-hole
        $container->addCompilerPass(new PublicForTestsCompilerPass());
    }
}

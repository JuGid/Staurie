<?php

namespace Jugid\Staurie\Component\Console;

use InvalidArgumentException;
use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Console\CoreFunctions\DebugFunction;
use Jugid\Staurie\Component\Console\CoreFunctions\ExitFunction;
use Jugid\Staurie\Component\Console\CoreFunctions\HelpFunction;

class Console extends AbstractComponent {

    private const CORE_FUNCTIONS = [
        ExitFunction::class,
        HelpFunction::class
    ];

    public array $functions;

    public function __construct()
    {
        $this->functions = [];
    }

    public function name() : string {
        return 'console';
    }

    public function getEventName() : array {
        return ['console.console', 'console.debug'];
    }

    public function initialize() : void {
        $this->addCoreFunctions();

        readline_completion_function(function($input, $index) {
            $functions = $this->functions;
            $matches = [];
            
            foreach($functions as $funct) {
                if(str_contains($funct->name(), $input)) {
                    $matches[] = $funct->name();
                }
            }

            return $matches;
        });
    }

    private function addCoreFunctions() : void {
        foreach(self::CORE_FUNCTIONS as $class) {
            $this->addFunction(new $class());
        }

        if($this->container->state()->isDevmode()) {
            $debug = DebugFunction::class;
            $this->addFunction(new $debug());
        }
    }

    public function addFunction(AbstractConsoleFunction $function) {
        if($this->functionAlreadyExists($function->name())) {
            throw new InvalidArgumentException('Function '. $function->name() . ' already exists');
        }

        $this->functions[] = $function;
        $function->setContainer($this->container);
        $function->initialize();
    }

    protected function action(string $event, array $arguments) : void {
        $input = readline('>> '); 
        $what_user_is_asking = explode(' ', $input);
        $command = $what_user_is_asking[0];
        $args = array_slice($what_user_is_asking, 1);
        $function = $this->getFunction($command, $args);

        if(null !== $function) {
            if(is_int($function->getArgs()) && count($args) !== $function->getArgs()) {
                echo "This function needs more arguments\n";
                return;
            }
            
            $function->action($args);
        } else {
            echo "Command ". $command . " does not exist\n";
        } 
    }

    private function functionAlreadyExists(string $name) : bool {
        foreach($this->functions as $function) {
            if($function->name() === $name) {
                return true;
            }
        }
        return false;
    }

    private function getFunction($name, $args) : ?AbstractConsoleFunction {
        foreach($this->functions as $function) {
            if($function->isTheFunctionINeed($name, $args)) {
                return $function;
            }
        }
        return null;
    }

    public function getFunctionsDefinition() : array {
        $functions = [];
        foreach($this->functions as $fn) {
            $functions[] = $fn->getFunctionAsArray();
        }
        return $functions;
    }

    public function defaultConfiguration() : array {
        return [];
    }

}
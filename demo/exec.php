<?php

use Cast\Model\Context\UnknownContextVariable;
use Cast\Service\Builder\RunnerBuilder;
use Cast\Service\Execution\Flow\ThrowResult;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Program\CodeHtmlPrinter;
use Cast\Service\Program\ValueHtmlPrinter;
use Cast\Service\Type\UnknownType;
use Cast\Service\Value\PhpToCastWithSubtypesValueTransformer;

require_once __DIR__ . '/../vendor/autoload.php';

$input = $argv ?? [
	...[0],
	...(($_GET['src'] ?? null) ? [$_GET['src']] : []),
	...(($_GET['gen'] ?? null) ? ['-g'] : []),
	...(($_GET['cached'] ?? null) ? ['-c'] : []),
	...($_GET['parameters'] ?? [])
];
array_shift($input);
$source = array_shift($input);
if (!preg_match("/^(cast\d+|demo-\w+)$/", $source)) {
	$source = 'cast7';
}
$generate = ($input[0] ?? null) === '-g' && array_shift($input);
$cached = ($input[0] ?? null) === '-c' && array_shift($input);

try {
	try {
	ob_start();
	$l = ["$source.cast", "core.cast", "lib.cast"];
	if ($source === 'cast31') {
		$l = ["cast31.cast", "core.cast", "httpcore.cast", "httpmiddleware.cast"];
	}

	$transformer = new PhpToCastWithSubtypesValueTransformer;

	$result = ($runner = (new RunnerBuilder(
		__DIR__ . '/Cast7.php',
		Cast7::class,
		__DIR__ . '/../cast-src',
		__DIR__ . '/../dev/jsonCache'
	)
	)->getRunner(... $l))->run( 'main', $transformer->fromPhpToCast($input));

	$printer = new ValueHtmlPrinter;
	$content = $printer->print($result);

		if (!($argv[1] ?? null)) {
			//header('Content-type: text/plain');
			if ($_GET['r'] ?? null) {
				//echo str_replace('RESULT = ', '', ob_get_clean());
				$output = ob_get_clean();
				$program = '';
			} else {
				$output = ob_get_clean();
				$codeHtmlPrinter = new CodeHtmlPrinter;
				$program = $codeHtmlPrinter->print($runner->program);
			}
		} else {
			echo ob_get_clean();
			$r = ($argv ?? null) ? "\033[31;1;1m" : '';
			$z = ($argv ?? null) ? "\033[34;1;1m" : '';
			$g = ($argv ?? null) ? "\033[37;1;2m" : '';
			$d = ($argv ?? null) ? "\033[0m" : '';
			echo "\n", $r, 'main' , $d , '(["', $g,
				implode($d . '","' . $g, $input), $d  . '"]);', PHP_EOL;
			echo "$z=>$d ", print_r($result, 1);
		}
		include __DIR__ . '/exec.tpl.php';
	} catch (ThrowResult $throwResult) {
		echo '<pre>', $runner->program, '</pre>';
		echo "Thrown Exception=", $throwResult->v, "***\n\n";
		//echo "... of type=", $valueTypeResolver->findTypeOf($throwResult->v), "***\n\n";
		echo '<pre>';
		throw $throwResult;
	} catch (AnalyserException $ex) {
		echo '<pre>', $ex, '</pre>';
		echo '<pre>', $runner->program, '</pre>';
	} catch (UnknownContextVariable $ex) {
		echo '<pre>', $runner->program, '</pre>';
		echo "Unknown variable ", $ex->variableName;
		echo '<pre>';
		throw $ex;
	} catch (UnknownType $ex) {
		//echo '<pre>', $runner->program, '</pre>';
		echo "Unknown type ", $ex->name;
		echo '<pre>';
		throw $ex;
	}
} catch (Exception $ex) {
	echo '<pre>', $ex;
}
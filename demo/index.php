<?php

use Cast\Model\Context\UnknownContextVariable;
use Cast\Service\Builder\RunnerBuilder;
use Cast\Service\Compiler\ProgramSourceCompiler;
use Cast\Service\Compiler\SourceToJsonConverter;
use Cast\Service\Execution\Flow\ThrowResult;
use Cast\Service\Expression\AnalyserException;
use Cast\Service\Program\Builder\FromArraySourceBuilder;
use Cast\Service\Program\Builder\ProgramBuilder;
use Cast\Service\Program\CodeHtmlPrinter;
use Cast\Service\Type\UnknownType;

require_once __DIR__ . '/../vendor/autoload.php';

$input = $argv ?? [
	...[0],
	...(($_GET['src'] ?? null) ? [$_GET['src']] : []),
];
array_shift($input);
$source = array_shift($input);
$sources = [];

$sourceRoot = __DIR__ . '/../cast-src';

foreach(glob("$sourceRoot/*.cast") as $sourceFile) {
	$sources[] = str_replace('.cast', '', basename($sourceFile));
}
if (!in_array($source, $sources, true)) {
	$source = 'cast31';
}
$qs = [
	'cast4' => '&parameters[]=4',
	'cast7' => '&parameters[]=3&parameters[]=4',
	'cast8' => '&parameters[]=29&parameters[]=15&parameters[]=51&parameters[]=31&parameters[]=211',
	'cast9' => '&parameters[]=30',
	'cast10' => '&parameters[]=3&parameters[]=4',
][$source] ?? '';
$generate = ($input[0] ?? null) === '-g' && array_shift($input);
$cached = ($input[0] ?? null) === '-c' && array_shift($input);

try {

	$parserPath = __DIR__ . '/Cast7.php';
	$parserClassName = Cast7::class;
	$jsonCacheDirectory = __DIR__ . '/../dev/jsonCache';

	$sourceToJsonConverter = new SourceToJsonConverter(
		$parserPath,
		$parserClassName
	);
	$programSourceCompiler = new ProgramSourceCompiler(
		$sourceRoot,
		$jsonCacheDirectory,
		$sourceToJsonConverter
	);
	$programBuilder = new ProgramBuilder(
		new FromArraySourceBuilder,
		$programSourceCompiler
	);
	$program = $programBuilder->buildProgramFromSource("$source.cast");
	$codeHtmlPrinter = new CodeHtmlPrinter;
	$content = $codeHtmlPrinter->print($program);
	include __DIR__ . '/code.tpl.php';
} catch (UnknownType $ex) {
	//echo '<pre>', $runner->program, '</pre>';
	echo "Unknown type ", $ex->name;
	echo '<pre>';
	throw $ex;
}

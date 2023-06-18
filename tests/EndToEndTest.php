<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/** @noinspection DuplicatedCode */

namespace Cast\Test;

use Cast\Model\Program\Literal\StringLiteral;
use Cast\Model\Runtime\Value\ListValue;
use Cast\Model\Runtime\Value\LiteralValue;
use Cast\Service\Builder\RunnerBuilder;
use Cast\Service\Compiler\ProgramSourceCompiler;
use Cast\Service\Compiler\SourceToJsonConverter;
use Cast\Service\Program\Builder\FromArraySourceBuilder;
use Cast\Service\Program\Builder\ProgramBuilder;
use Cast7;
use JsonException;
use PHPUnit\Framework\TestCase;

final class EndToEndTest extends TestCase {

	private array $sources;
	private RunnerBuilder $runnerBuilder;
	private string $baseDir;
	private FromArraySourceBuilder $fromArraySourceBuilder;

	protected function setUp(): void {
		parent::setUp();

		chdir(__DIR__ . '/../demo');

		$this->baseDir = __DIR__ . '/../demo';
		$this->sources = [
			... array_map(static fn(int $num): string => "cast$num.cast", range(4, 29)),
			... [
				'demo-alias.cast',
				'demo-any.cast',
				'demo-array.cast',
				'demo-boolean.cast',
				'demo-db.cast',
				'demo-enum.cast',
				'demo-hydrate.cast',
				'demo-integer.cast',
				'demo-intersection.cast',
				'demo-json.cast',
				'demo-map.cast',
				'demo-mutable.cast',
				'demo-real.cast',
				'demo-string.cast',
				'demo-subtype.cast',
				'demo-type.cast',
				'demo-union.cast',
			]
		];
		$this->runnerBuilder = new RunnerBuilder(
			$parserPath = $this->baseDir . '/Cast7.php',
			$parserClassName = Cast7::class,
			$sourceRoot = __DIR__ . '/../cast-src',
			$jsonCacheDirectory = __DIR__ . '/../dev/jsonCache'
		);
		$this->fromArraySourceBuilder = new FromArraySourceBuilder;

		$sourceToJsonConverter = new SourceToJsonConverter(
			$parserPath,
			$parserClassName
		);
		$programSourceCompiler = new ProgramSourceCompiler(
			$sourceRoot,
			$jsonCacheDirectory,
			$sourceToJsonConverter
		);
		$this->programBuilder = new ProgramBuilder(
			new FromArraySourceBuilder,
			$programSourceCompiler
		);
	}

	/** @throws JsonException */
	public function testSerialization(): void {
		foreach($this->sources as $source) {
			$program = $this->programBuilder->buildProgramFromSource($source);
			$program2 = $this->fromArraySourceBuilder->build(
				json_decode(
					json_encode($program, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
					true
				)
			);
			self::assertEquals($program, $program2);
			self::assertStringContainsString('->', (string)$program);
		}
	}


	public function testRun(): void {
		foreach($this->sources as $source) {
			/*try {*/
				$runner = $this->runnerBuilder->getRunner($source, "core.cast", "lib.cast");
				$result = $runner->run( 'main', new ListValue(
					new LiteralValue(new StringLiteral("3")),
					new LiteralValue(new StringLiteral("4")),
				));
			/*} catch (ThrowResult|AnalyserException $ex) {
				echo "[Throw = $source]";
				var_dump($source);
				echo $ex;
				die;
			}*/
			self::assertNotEquals(-1, $result);
		}
	}

}
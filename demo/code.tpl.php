<?php
/**
 * @var string $content
 * @var string $source
 * @var string $qs
 * @var string[] $sources
 */
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title></title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<style>
			main {
				display: grid;
				grid-template-columns: 2fr 1fr;
			}
			#source {
				height: calc(100vh - 120px);
				overflow-y: scroll;
			}
			#run {
				width: 100%;
				height: calc(100vh - 120px);
			}
		</style>
	</head>
	<body>
		<header>
			<h4>Select file:</h4>
			<?php foreach($sources as $sourceF) { ?>
				<?php if ($source === $sourceF) { ?>
					<em><?= $sourceF ?></em>
					<?php if (preg_match('/^(cast\d+|demo-\w+)$/', $sourceF)) { ?>
						<a target="run" style="color: fuchsia" href="exec.php?r=1&src=<?= $sourceF ?><?= $qs ?>">[execute]</a>
					<?php } ?>
				<?php } else { ?>
					<a href="?src=<?= $sourceF ?>"><?= $sourceF ?></a>
				<?php } ?>
			<?php } ?>
		</header>
		<main>
			<div id="source">
				<?= $content ?>
			</div>
			<iframe id="run" name="run" src="about:blank">

			</iframe>
		</main>
	</body>
</html>
<?php
/**
 * @var string $content
 * @var string $program
 * @var string $output
 */
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title></title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	<body>
		<main>
			<details open>
				<summary>Result</summary>
				<div id="value">
					<?= $content ?>
				</div>
			</details>
			<?php if (trim($output) !== '') { ?>
				<details open>
					<summary>Stdout</summary>
					<div id="value">
						<?= $output ?: '--- no output ---' ?>
					</div>
				</details>
			<?php } ?>
			<?php if (trim($program) !== '') { ?>
				<details>
					<summary>Program</summary>
					<div id="value">
						<?= $program ?>
					</div>
				</details>
			<?php } ?>
		</main>
	</body>
</html>
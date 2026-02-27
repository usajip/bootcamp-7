<?php
require_once __DIR__ . '/navbar.php';

function renderTemplate(
	string $title,
	string $content,
	string $activeMenu = 'home',
	string $basePath = '',
	string $extraHead = '',
	string $extraScript = ''
): void
{
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
		<?php echo $extraHead; ?>
	</head>
	<body class="bg-light">
		<?php renderNavbar($activeMenu, $basePath); ?>

		<main class="container py-4">
			<?php echo $content; ?>
		</main>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
		<?php echo $extraScript; ?>
	</body>
	</html>
	<?php
}

